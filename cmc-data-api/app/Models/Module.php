<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Module (teaching unit) sourced from AvancementProgramme.xlsx.
 *
 * Excel mapping:
 *   Code Module        → code_module  (not globally unique; unique key is code_module + annee_id)
 *   Module             → libelle
 *   Régional           → regional     boolean (regional/shared module)
 *   Année de formation → annee_id     (via annee relationship)
 *   Code Filière       → annee.filiere_code
 *
 *   MHP Totale DRIF    → mh_presentiel  (planned présentiel hours)
 *   MHSYN Totale DRIF  → mh_syn         (planned synchrone hours)
 *   MHASYN Totale DRIF → mh_asyn        (planned asynchrone hours)
 *   MH Totale DRIF     → mh_totale      (planned total hours — stored, not computed)
 *
 * Planned hours (mh_*) are properties of the module itself (constant across every
 * groupe that takes it), distinct from Affectation::mh_affecte (hours assigned to
 * a specific groupe+formateur) and Avancement::mh_realisee_* (hours delivered).
 */
class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_module',
        'annee_id',
        'libelle',
        'regional',
        'mh_presentiel',
        'mh_syn',
        'mh_asyn',
        'mh_totale',
    ];

    protected function casts(): array
    {
        return [
            'regional'      => 'boolean',
            'mh_presentiel' => 'decimal:2',
            'mh_syn'        => 'decimal:2',
            'mh_asyn'       => 'decimal:2',
            'mh_totale'     => 'decimal:2',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @return BelongsTo<Annee, Module> */
    public function annee(): BelongsTo
    {
        return $this->belongsTo(Annee::class);
    }

    /** @return HasMany<Affectation> */
    public function affectations(): HasMany
    {
        return $this->hasMany(Affectation::class);
    }

    /** @return HasMany<Avancement> */
    public function avancements(): HasMany
    {
        return $this->hasMany(Avancement::class); // module_id / id — convention defaults
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
     * Arithmetic sum of the three planned-hours components.
     * Distinct from mh_totale (the authoritative stored DRIF total) — use
     * this when you need the computed sum rather than the spreadsheet figure,
     * which can differ slightly due to upstream rounding.
     */
    protected function mhPlanifieeSomme(): Attribute
    {
        return Attribute::make(
            get: fn () => (float) $this->mh_presentiel + (float) $this->mh_syn + (float) $this->mh_asyn,
        );
    }
}
