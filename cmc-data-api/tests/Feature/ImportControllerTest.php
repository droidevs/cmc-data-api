<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

/**
 * Feature tests for POST /api/v1/import.
 *
 * Each test builds an in-memory spreadsheet, writes it to a temp file,
 * uploads it via the HTTP layer, and asserts the JSON response + database
 * state.
 */
class ImportControllerTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build a real .xlsx UploadedFile from a 2-D data array.
     * First sub-array is the header row.
     *
     * @param  array<int, array<int, mixed>>  $data
     */
    private function makeXlsx(array $data): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        foreach ($data as $rowIndex => $row) {
            foreach (array_values($row) as $colIndex => $value) {
                // PhpSpreadsheet v2+ removed setCellValueByColumnAndRow(); use
                // the coordinate helper instead: column A=1, B=2 …
                $col  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
                $cell = $col . ($rowIndex + 1);
                $sheet->setCellValue($cell, $value);
            }
        }

        $tmpPath = tempnam(sys_get_temp_dir(), 'import_test_') . '.xlsx';
        (new Xlsx($spreadsheet))->save($tmpPath);

        return new UploadedFile(
            $tmpPath,
            'test.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true  // test mode – skip is_uploaded_file() check
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Validation tests
    // ─────────────────────────────────────────────────────────────────────────

    public function test_import_requires_file(): void
    {
        $response = $this->postJson('/api/v1/import', ['type' => 'formateurs']);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['file']);
    }

    public function test_import_requires_type(): void
    {
        $file     = $this->makeXlsx([['Mle', 'Nom et Prénom']]);
        $response = $this->postJson('/api/v1/import', [
            'file' => $file,
            'type' => '',
        ]);
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['type']);
    }

    public function test_import_rejects_invalid_type(): void
    {
        $file     = $this->makeXlsx([['Mle', 'Nom et Prénom']]);
        $response = $this->postJson('/api/v1/import', [
            'file' => $file,
            'type' => 'wrong_type',
        ]);
        $response->assertStatus(422);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Formateur import tests
    // ─────────────────────────────────────────────────────────────────────────

    public function test_imports_formateurs_successfully(): void
    {
        $file = $this->makeXlsx([
            ['Mle', 'Nom et Prénom', 'MHS', 'Statut', 'Affectation', 'EFP Mutualisé', 'Mutualisé', 'Email Edu'],
            ['19307', 'Ahmed Benali',  26,   'OFPPT',  'Pole Numerique', 'CMC', 'Oui', 'a.benali@edu.ma'],
            ['H4182', 'Sara El Amri',  20,   'Vacataire', 'Pole Digital', '', 'Non', 's.elamri@edu.ma'],
        ]);

        $response = $this->post('/api/v1/import', [
            'file' => $file,
            'type' => 'formateurs',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('summary.inserted', 2)
                 ->assertJsonPath('summary.errors', 0);

        $this->assertDatabaseHas('formateurs', ['mle' => '19307', 'nom_prenom' => 'Ahmed Benali']);
        $this->assertDatabaseHas('formateurs', ['mle' => 'H4182', 'nom_prenom' => 'Sara El Amri']);
        $this->assertDatabaseHas('poles',      ['libelle' => 'Pole Numerique']);
    }

    public function test_formateur_upsert_updates_existing_record(): void
    {
        // Initial import — include Affectation so pole_id is set
        $file1 = $this->makeXlsx([
            ['Mle', 'Nom et Prénom', 'Statut', 'Affectation'],
            ['99001', 'Old Name', 'OFPPT', 'Pole Test'],
        ]);
        $this->post('/api/v1/import', ['file' => $file1, 'type' => 'formateurs']);

        // Second import with updated name (same pole)
        $file2 = $this->makeXlsx([
            ['Mle', 'Nom et Prénom', 'Statut', 'Affectation'],
            ['99001', 'New Name', 'Vacataire', 'Pole Test'],
        ]);
        $response = $this->post('/api/v1/import', ['file' => $file2, 'type' => 'formateurs']);

        $response->assertStatus(200)
                 ->assertJsonPath('summary.updated', 1)
                 ->assertJsonPath('summary.inserted', 0);

        $this->assertDatabaseHas('formateurs', ['mle' => '99001', 'nom_prenom' => 'New Name', 'statut' => 'Vacataire']);
    }

    public function test_formateur_insert_only_skips_existing(): void
    {
        $file1 = $this->makeXlsx([
            ['Mle', 'Nom et Prénom', 'Affectation'],
            ['88001', 'Original Name', 'Pole Test'],
        ]);
        $this->post('/api/v1/import', ['file' => $file1, 'type' => 'formateurs', 'mode' => 'upsert']);

        $file2 = $this->makeXlsx([
            ['Mle', 'Nom et Prénom', 'Affectation'],
            ['88001', 'Should Not Change', 'Pole Test'],
        ]);
        $response = $this->post('/api/v1/import', ['file' => $file2, 'type' => 'formateurs', 'mode' => 'insert_only']);

        $response->assertStatus(200)
                 ->assertJsonPath('summary.skipped', 1)
                 ->assertJsonPath('summary.inserted', 0);

        $this->assertDatabaseHas('formateurs', ['mle' => '88001', 'nom_prenom' => 'Original Name']);
    }

    public function test_formateur_row_with_missing_mle_reports_row_error(): void
    {
        $file = $this->makeXlsx([
            ['Mle', 'Nom et Prénom'],
            ['',    'No Mle Person'],
        ]);

        $response = $this->post('/api/v1/import', ['file' => $file, 'type' => 'formateurs']);

        // 207 Multi-Status because some rows had errors
        $response->assertStatus(207)
                 ->assertJsonPath('summary.errors', 1);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Stagiaire import tests
    // ─────────────────────────────────────────────────────────────────────────

    public function test_imports_stagiaires_successfully(): void
    {
        $file = $this->makeXlsx([
            // Match the Excel schema from the screenshots
            ['CEF',            'CNI',        'NOM',    'PRENO',  'DATE D',     'GENRE', 'TELEPH',      'ACTIF', 'GROUP',     'FILIERE',          'CODE_I',    'NIVEAU', 'SECTEU',        'TYPE DI'],
            ['2006091400263',  'AB123456',   'ALAMI',  'Youssef','15/09/2006', 'H',     '0612345678',  'Oui',   'DEV101',   'Dev Web FS',       'DEV_TS',    'TS',     'Pole Numerique', 'TMSIR'],
            ['2007020400224',  'CD789012',   'AMRANI', 'Sara',   '04/02/2007', 'F',     '0698765432',  'Oui',   'DEV101',   'Dev Web FS',       'DEV_TS',    'TS',     'Pole Numerique', 'TMSIR'],
        ]);

        $response = $this->post('/api/v1/import', [
            'file' => $file,
            'type' => 'stagiaires',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('summary.inserted', 2)
                 ->assertJsonPath('summary.errors', 0);

        $this->assertDatabaseHas('stagiaires', ['cef' => '2006091400263', 'nom' => 'ALAMI']);
        $this->assertDatabaseHas('stagiaires', ['cef' => '2007020400224', 'nom' => 'AMRANI']);
        // Hierarchy must have been created
        $this->assertDatabaseHas('poles',   ['libelle' => 'Pole Numerique']);
        $this->assertDatabaseHas('niveaux', ['libelle' => 'TS']);
        $this->assertDatabaseHas('filieres', ['code_filiere' => 'DEV_TS']);
        $this->assertDatabaseHas('groupes', ['code' => 'DEV101']);
    }

    public function test_stagiaire_missing_cef_reports_error(): void
    {
        $file = $this->makeXlsx([
            ['CEF', 'NOM',    'GROUP'],
            ['',    'ALAMI',  'DEV101'],
        ]);

        $response = $this->post('/api/v1/import', ['file' => $file, 'type' => 'stagiaires']);

        $response->assertStatus(207)
                 ->assertJsonPath('summary.errors', 1);
    }

    public function test_import_creates_shared_pole_only_once(): void
    {
        $file = $this->makeXlsx([
            ['Mle', 'Nom et Prénom', 'Affectation'],
            ['A001', 'Trainer One', 'Pole Numerique'],
            ['A002', 'Trainer Two', 'Pole Numerique'],
            ['A003', 'Trainer Three', 'Pole Numerique'],
        ]);

        $this->post('/api/v1/import', ['file' => $file, 'type' => 'formateurs']);

        $this->assertEquals(1, \App\Models\Pole::where('libelle', 'Pole Numerique')->count());
    }
}
