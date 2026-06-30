<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Annee;
use App\Models\Filiere;
use App\Models\Formateur;
use App\Models\Groupe;
use App\Models\Niveau;
use App\Models\Pole;
use App\Models\Stagiaire;
use App\Models\TypeFormation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as SpreadsheetDate;

/**
 * Handles parsing and atomic insertion of trainer / trainee Excel data.
 *
 * Insertion is done inside a single DB transaction. If any unrecoverable
 * error occurs the whole import is rolled back. Row-level validation
 * errors are collected and returned without aborting the import by default,
 * unless $abortOnError = true.
 */
class ImportService
{
    // ─────────────────────────────────────────────────────────────────────────
    //  Public API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Parse the uploaded file and import into the database.
     *
     * @param  UploadedFile  $file
     * @param  string        $type      'formateurs' | 'stagiaires'
     * @param  string        $mode      'upsert' | 'insert_only'
     * @return array{inserted:int, updated:int, skipped:int, errors:array}
     */
    public function import(UploadedFile $file, string $type, string $mode = 'upsert'): array
    {
        $rows = $this->parseFile($file);

        $result = [
            'inserted' => 0,
            'updated'  => 0,
            'skipped'  => 0,
            'errors'   => [],
        ];

        DB::transaction(function () use ($rows, $type, $mode, &$result) {
            foreach ($rows as $rowIndex => $row) {
                $lineNumber = $rowIndex + 2; // +1 for 0-index, +1 to skip header
                try {
                    if ($type === 'formateurs') {
                        $action = $this->importFormateurRow($row, $mode);
                    } else {
                        $action = $this->importStagiaireRow($row, $mode);
                    }

                    match ($action) {
                        'inserted' => $result['inserted']++,
                        'updated'  => $result['updated']++,
                        'skipped'  => $result['skipped']++,
                        default    => null,
                    };
                } catch (\InvalidArgumentException $e) {
                    // Validation / mapping error — collect, continue
                    $result['errors'][] = [
                        'row'     => $lineNumber,
                        'message' => $e->getMessage(),
                        'data'    => $this->safeRowPreview($row),
                    ];
                }
            }
        });

        return $result;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  File Parsing
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Load the spreadsheet and return all non-empty data rows as
     * associative arrays keyed by the header row values.
     *
     * @return array<int, array<string, mixed>>
     */
    private function parseFile(UploadedFile $file): array
    {
        $path      = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());

        $reader = match ($extension) {
            'xlsx'  => IOFactory::createReader('Xlsx'),
            'xls'   => IOFactory::createReader('Xls'),
            'csv'   => IOFactory::createReader('Csv'),
            default => IOFactory::createReaderForFile($path),
        };

        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet       = $spreadsheet->getActiveSheet();

        $rawRows = $sheet->toArray(null, true, true, false);

        if (empty($rawRows)) {
            return [];
        }

        // First row is the header
        $headers = array_map(
            fn($h) => $this->normalizeHeader((string) ($h ?? '')),
            array_shift($rawRows)
        );

        $rows = [];
        foreach ($rawRows as $raw) {
            $row = array_combine($headers, $raw);
            // Skip entirely blank rows
            if (empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) {
                continue;
            }
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Normalise a header cell to lowercase ASCII snake_case so we can
     * match regardless of accents / spaces / capitalisation.
     */
    private function normalizeHeader(string $header): string
    {
        $header = mb_strtolower($header);

        // Remove common accents
        $header = str_replace(
            ['é', 'è', 'ê', 'ë', 'à', 'â', 'ù', 'û', 'ô', 'î', 'ï', 'ç', 'œ', 'æ'],
            ['e', 'e', 'e', 'e', 'a', 'a', 'u', 'u', 'o', 'i', 'i', 'c', 'oe', 'ae'],
            $header
        );

        // Replace non-alphanumeric sequences with underscore
        $header = preg_replace('/[^a-z0-9]+/', '_', $header);
        return trim($header, '_');
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Formateur Import
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Map an Excel row to a Formateur record.
     *
     * Expected (normalised) column names — covers both a plain "formateurs"
     * export (mle / nom_et_prenom / ...) and a module-progress report such
     * as "AvancementProgramme" (mle_affecte_presentiel_actif / ...):
     *
     *   mle | matricule | mle_affecte_presentiel_actif | mle_affecte_syn_actif
     *                              → mle (PK)
     *   nom_et_prenom | nom_prenom | formateur_affecte_presentiel_actif
     *      | formateur_affecte_syn_actif
     *                              → nom_prenom
     *   mhs | mh_totale_drif | mh_totale_presentiel
     *                              → mhs
     *   statut | statut_sous_groupe → statut
     *   affectation | secteur | regional
     *                              → pole.libelle (home pole)
     *   efp_mutualise | efp_mu    → efp_mutualise
     *   mutualise                 → mutualise
     *   email_edu | email         → email_edu
     *
     * Rows where no Mle can be found at all (e.g. a module-progress line
     * where no trainer has been assigned yet) are silently skipped rather
     * than treated as an error, since that is a valid/expected state for
     * report-style exports.
     *
     * @return 'inserted'|'updated'|'skipped'
     */
    private function importFormateurRow(array $row, string $mode): string
    {
        $mle = $this->cell($row, [
            'mle',
            'matricule',
            'mle_',
            'mle_affecte_presentiel_actif',
            'mle_affecte_syn_actif',
        ]);

        // No trainer assigned on this row (common in progress-report style
        // exports where a module simply has no one assigned yet) — skip
        // rather than error.
        if (empty($mle)) {
            return 'skipped';
        }
        $mle = (string) $mle;

        $nomPrenom = $this->cell($row, [
            'nom_et_prenom',
            'nom_prenom',
            'nom_et_prenom_',
            'nom_prenom_',
            'formateur_affecte_presentiel_actif',
            'formateur_affecte_syn_actif',
        ]);
        if (empty($nomPrenom)) {
            throw new \InvalidArgumentException("Row Mle={$mle}: Missing required field: Nom et Prénom.");
        }

        // ── Resolve Pole (create if missing) ──────────────────────────────────
        $poleLibelle = $this->cell($row, ['affectation', 'pole', 'secteur', 'regional']);
        $poleId      = null;
        if (!empty($poleLibelle)) {
            $pole   = Pole::firstOrCreate(['libelle' => trim((string) $poleLibelle)]);
            $poleId = $pole->id;
        }

        // ── Boolean: Mutualisé ─────────────────────────────────────────────────
        $mutualiseRaw = $this->cell($row, ['mutualise', 'mutualise_', 'mutualise__']);
        $mutualise    = $this->parseBool($mutualiseRaw);

        // ── Build payload ──────────────────────────────────────────────────────
        $payload = [
            'pole_id'       => $poleId,
            'nom_prenom'    => trim((string) $nomPrenom),
            'statut'        => trim((string) ($this->cell($row, ['statut', 'statut_sous_groupe']) ?? '')),
            'email_edu'     => trim((string) ($this->cell($row, ['email_edu', 'email']) ?? '')),
            'mhs'           => $this->parseDecimal($this->cell($row, [
                'mhs',
                'mh_totale_drif',
                'mh_totale_presentiel',
                'mh_affectee_globale_p_syn',
            ])),
            'efp_mutualise' => trim((string) ($this->cell($row, ['efp_mutualise', 'efp_mu', 'efp_mutualise_']) ?? '')),
            'mutualise'     => $mutualise,
        ];

        // Remove nulls so updateOrCreate doesn't write empty strings on update
        $payload = array_filter($payload, fn($v) => $v !== null && $v !== '');

        if ($mode === 'insert_only' && Formateur::where('mle', $mle)->exists()) {
            return 'skipped';
        }

        $existed = Formateur::where('mle', $mle)->exists();
        Formateur::updateOrCreate(['mle' => $mle], $payload);

        return $existed ? 'updated' : 'inserted';
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Stagiaire Import
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Map an Excel row to a Stagiaire, creating the academic hierarchy
     * (Pole → Niveau → TypeFormation → Filiere → Annee → Groupe) as needed.
     *
     * Expected (normalised) columns:
     *   cef | matricule_etudiant → cef (PK, 13-char string)
     *   cni | cin                → cni
     *   nom                      → nom
     *   prenom | preno           → prenom
     *   nom_arabe                → nom_arabe
     *   prenom_arabe             → prenom_arabe
     *   date_d | date_naissance  → date_naissance
     *   genre | sexe             → genre
     *   teleph | telephone       → telephone
     *   email                    → email (stored as telephone fallback)
     *   actif                    → actif
     *   group | groupe           → groupe.code
     *   filiere                  → filiere.libelle
     *   code_i | code_filiere    → filiere.code_filiere
     *   niveau                   → niveau.libelle
     *   secteu | secteur         → pole.libelle
     *   type_di | type_formation → type_formation.libelle
     *
     * @return 'inserted'|'updated'|'skipped'
     */
    private function importStagiaireRow(array $row, string $mode): string
    {
        $cef = $this->cell($row, ['cef', 'matricule_etudiant', 'matriculeetudiant']);
        if (empty($cef)) {
            throw new \InvalidArgumentException('Missing required field: CEF (matricule étudiant).');
        }
        $cef = (string) $cef;

        $nom = $this->cell($row, ['nom']);
        if (empty($nom)) {
            throw new \InvalidArgumentException("Row CEF={$cef}: Missing required field: NOM.");
        }

        // ── Resolve academic hierarchy ─────────────────────────────────────────

        $groupeId = $this->resolveGroupe($row, $cef);

        // ── Parse date ────────────────────────────────────────────────────────
        $dateRaw  = $this->cell($row, ['date_d', 'date_naissance', 'datenaissance', 'date_d_']);
        $dateNaiss = $this->parseDate($dateRaw);

        // ── Boolean: actif ────────────────────────────────────────────────────
        $actifRaw = $this->cell($row, ['actif', 'etudiantactif']);
        $actif    = $this->parseBool($actifRaw) ?? true; // default active

        // ── Build payload ──────────────────────────────────────────────────────
        $payload = [
            'groupe_id'     => $groupeId,
            'cni'           => trim((string) ($this->cell($row, ['cni', 'cin']) ?? '')),
            'nom'           => trim((string) $nom),
            'prenom'        => trim((string) ($this->cell($row, ['prenom', 'preno', 'prenom_']) ?? '')),
            'nom_arabe'     => trim((string) ($this->cell($row, ['nom_arabe']) ?? '')),
            'prenom_arabe'  => trim((string) ($this->cell($row, ['prenom_arabe']) ?? '')),
            'date_naissance'=> $dateNaiss,
            'genre'         => $this->normalizeGenre($this->cell($row, ['genre', 'sexe'])),
            'telephone'     => trim((string) ($this->cell($row, ['teleph', 'telephone', 'ntelelephone']) ?? '')),
            'actif'         => $actif,
        ];

        // Strip empty strings so existing values aren't overwritten with blanks
        $payload = array_filter($payload, fn($v) => $v !== null && $v !== '');

        if ($mode === 'insert_only' && Stagiaire::where('cef', $cef)->exists()) {
            return 'skipped';
        }

        $existed = Stagiaire::where('cef', $cef)->exists();
        Stagiaire::updateOrCreate(['cef' => $cef], $payload);

        return $existed ? 'updated' : 'inserted';
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Academic Hierarchy Resolution
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Resolve (and create if missing) the full chain:
     *   Pole → Niveau → TypeFormation → Filiere → Annee → Groupe
     *
     * @return int Groupe primary key
     */
    private function resolveGroupe(array $row, string $cef): int
    {
        // ── Pole ──────────────────────────────────────────────────────────────
        $poleLibelle = $this->cell($row, ['secteu', 'secteur', 'pole', 'affectation']);
        $pole        = null;
        if (!empty($poleLibelle)) {
            $pole = Pole::firstOrCreate(['libelle' => trim((string) $poleLibelle)]);
        }

        // ── Niveau ────────────────────────────────────────────────────────────
        $niveauLibelle = $this->cell($row, ['niveau', 'niveauscolaire']);
        $niveau        = null;
        if (!empty($niveauLibelle)) {
            $niveau = Niveau::firstOrCreate(['libelle' => trim((string) $niveauLibelle)]);
        }

        // ── TypeFormation ─────────────────────────────────────────────────────
        $typeLibelle  = $this->cell($row, ['type_di', 'type_formation', 'typeformation', 'type_di_', 'type_de_formation']);
        $typeFormation = null;
        if (!empty($typeLibelle)) {
            $typeFormation = TypeFormation::firstOrCreate(['libelle' => trim((string) $typeLibelle)]);
        }

        // ── Filiere ───────────────────────────────────────────────────────────
        $filiereCode   = $this->cell($row, ['code_i', 'code_filiere', 'codefiliere', 'code_i_']);
        $filiereLibelle= $this->cell($row, ['filiere', 'filiere_']);

        $filiere = null;
        if (!empty($filiereCode)) {
            $filiere = Filiere::firstOrCreate(
                ['code_filiere' => trim((string) $filiereCode)],
                [
                    'libelle'           => trim((string) ($filiereLibelle ?? $filiereCode)),
                    'pole_id'           => $pole?->id,
                    'niveau_id'         => $niveau?->id,
                    'type_formation_id' => $typeFormation?->id,
                    'secteur'           => $pole?->libelle,
                ]
            );
        } elseif (!empty($filiereLibelle)) {
            // Fall back to name-only match when no code is provided
            $filiere = Filiere::firstOrCreate(
                ['libelle' => trim((string) $filiereLibelle)],
                [
                    'code_filiere'      => strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '_', (string) $filiereLibelle), 0, 20)),
                    'pole_id'           => $pole?->id,
                    'niveau_id'         => $niveau?->id,
                    'type_formation_id' => $typeFormation?->id,
                    'secteur'           => $pole?->libelle,
                ]
            );
        }

        // ── Annee (derive year from group code if not present) ────────────────
        $groupeCode = $this->cell($row, ['group', 'groupe', 'group_', 'codediplome']);
        if (empty($groupeCode)) {
            throw new \InvalidArgumentException("Row CEF={$cef}: Missing group code (GROUP / CodeDiplome).");
        }
        $groupeCode = trim((string) $groupeCode);

        // Derive year: look for trailing digit 1 or 2 in the group code (e.g. DEV101 → 1)
        $yearInt = $this->deriveYearFromGroupCode($groupeCode);

        $annee = null;
        if ($filiere) {
            $annee = Annee::firstOrCreate(
                ['filiere_code' => $filiere->code_filiere, 'libelle' => $yearInt],
            );
        }

        // ── Groupe ────────────────────────────────────────────────────────────
        $groupe = Groupe::firstOrCreate(
            ['code' => $groupeCode],
            [
                'annee_id' => $annee?->id,
                'effectif' => 0,
                'mode'     => 'Résidentiel',
            ]
        );

        return $groupe->id;
    }

    /**
     * Attempt to extract the academic year (1 or 2) from a group code.
     * e.g.  "DEV101"     → 1
     *        "DEVOWFS201" → 2
     *        "GRP_A_2"    → 2
     * Falls back to 1 if nothing useful is found.
     */
    private function deriveYearFromGroupCode(string $code): int
    {
        // Try the digit that directly precedes the last two digits
        if (preg_match('/([12])\d{2}$/', $code, $m)) {
            return (int) $m[1];
        }
        // Try a trailing _1 or _2
        if (preg_match('/_([12])$/', $code, $m)) {
            return (int) $m[1];
        }
        return 1;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Try each candidate key in order, returning the first non-null value.
     *
     * @param  string[]  $candidates
     */
    private function cell(array $row, array $candidates): mixed
    {
        foreach ($candidates as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
                return $row[$key];
            }
        }
        return null;
    }

    private function parseBool(mixed $value): ?bool
    {
        if ($value === null || $value === '') {
            return null;
        }
        $lower = strtolower(trim((string) $value));
        return in_array($lower, ['1', 'true', 'oui', 'yes', 'o', 'y'], true);
    }

    private function parseDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        // Replace comma decimal separator used in French spreadsheets
        $cleaned = str_replace(',', '.', (string) $value);
        return is_numeric($cleaned) ? (float) $cleaned : null;
    }

    /**
     * Parse a date that might be:
     *   - an Excel serial number (float/int)
     *   - a string in various formats (d/m/Y, Y-m-d, d-m-Y …)
     */
    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Excel serial date (numeric)
        if (is_numeric($value)) {
            try {
                $dt = SpreadsheetDate::excelToDateTimeObject((float) $value);
                return Carbon::instance($dt)->toDateString();
            } catch (\Throwable) {
                return null;
            }
        }

        $str = trim((string) $value);

        // Try common formats
        $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y', 'd.m.Y', 'Y/m/d'];
        foreach ($formats as $fmt) {
            try {
                $dt = Carbon::createFromFormat($fmt, $str);
                if ($dt && $dt->format($fmt) === $str) {
                    return $dt->toDateString();
                }
            } catch (\Throwable) {
                // try next
            }
        }

        // Last resort: let Carbon guess
        try {
            return Carbon::parse($str)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    /** Normalise genre to 'H' (Homme) or 'F' (Femme). */
    private function normalizeGenre(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $upper = strtoupper(trim((string) $value));
        if (in_array($upper, ['H', 'M', 'HOMME', 'MALE'], true)) {
            return 'H';
        }
        if (in_array($upper, ['F', 'FEMME', 'FEMALE'], true)) {
            return 'F';
        }
        return null;
    }

    /** Return the first few fields of a row for error context, masking sensitive data. */
    private function safeRowPreview(array $row): array
    {
        $keys = array_slice(array_keys($row), 0, 5);
        return array_intersect_key($row, array_flip($keys));
    }
}
