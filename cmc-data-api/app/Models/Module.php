<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Module (teaching unit) sourced from AvancementProgramme.xlsx.
 *
 * Excel mapping:
 *   Code Module        → code_module  (PK, string e.g. "M101", "EGTS102")
 *   Module              → libelle
 *   Régional            → regional     boolean (whether it's a regional/shared module)
 *   Année de formation  → annee_id     (via annee relationship)
 *   Code Filière        → annee.filiere_code
 *
 *   MHP Totale DRIF     → mh_presentiel ← NEW: planned présentiel hours
 *   MHSYN Totale DRIF   → mh_syn        ← NEW: planned synchrone hours
 *   MHASYN Totale DRIF  → mh_asyn       ← NEW: planned asynchrone hours
 *   MH Totale  DRIF     → mh_totale     ← NEW: planned total hours (stored,
 *                                          not purely computed — see migration)
 *
 * Note: the same code_module can appear in multiple filieres/annees with different
 * libelles (e.g. "EGTS202" is "Français" across all TS filieres).
 * The unique key is (code_module, annee_id).
 *
 * Planned hours (mh_presentiel/mh_syn/mh_asyn/mh_totale) are properties of
 * the module itself (constant across every groupe that takes it), distinct
 * from Affectation::mh_affecte / mh_affecte_syn (hours assigned to one
 * specific groupe+formateur pairing) and from Avancement::mh_realisee_*
 * (hours actually delivered for one specific groupe).
 */
class Module extends Model
{
    protected $primaryKey = 'code_module';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'code_module',
        'annee_id',
        'libelle',
        'regional',
        'mh_presentiel', // ← NEW: "MHP Totale DRIF"
        'mh_syn',        // ← NEW: "MHSYN Totale DRIF"
        'mh_asyn',       // ← NEW: "MHASYN Totale DRIF"
        'mh_totale',     // ← NEW: "MH Totale  DRIF"
    ];

    protected $casts = [
        'regional'      => 'boolean',
        'mh_presentiel' => 'decimal:2',
        'mh_syn'        => 'decimal:2',
        'mh_asyn'       => 'decimal:2',
        'mh_totale'     => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @return BelongsTo<Annee, Module> */
    public function annee(): BelongsTo
    {
        return $this->belongsTo(Annee::class);
    }

    /** @return HasMany<Affectation> */
    public function affectations(): HasMany
    {
        return $this->hasMany(Affectation::class, 'module_code', 'code_module');
    }

    /** @return HasMany<Avancement> */
    public function avancements(): HasMany
    {
        return $this->hasMany(Avancement::class, 'module_id', 'id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeRegional(Builder $query): Builder
    {
        return $query->where('regional', true);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('libelle', 'like', "%{$term}%")
            ->orWhere('code_module', 'like', "%{$term}%");
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /**
     * Sum of the three planned-hours components. Distinct from mh_totale
     * (the stored DRIF total) — exposed for cases where the caller wants
     * the arithmetic sum rather than the authoritative spreadsheet figure,
     * which can differ slightly due to upstream rounding.
     */
    public function getMhPlanifieeSommeAttribute(): float
    {
        return (float) $this->mh_presentiel + (float) $this->mh_syn + (float) $this->mh_asyn;
    }
}
