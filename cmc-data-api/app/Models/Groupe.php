<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Groupe (class group) sourced from AvancementProgramme.xlsx.
 *
 * Excel mapping:
 *   Groupe           → code       (e.g. "DEV101", "DEVOWFS201")
 *   Effectif Groupe  → effectif
 *   Mode             → mode       "Résidentiel" | "Alternance" …
 *   Code Filière     → annee.filiere_code
 *   Année de formation → annee.libelle
 */
class Groupe extends Model
{
    use HasFactory;

    protected $fillable = [
        'annee_id',
        'code',
        'effectif',
        'mode',
    ];

    protected $casts = [
        'effectif' => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @return BelongsTo<Annee, Groupe> */
    public function annee(): BelongsTo
    {
        return $this->belongsTo(Annee::class);
    }

    /** @return HasMany<Stagiaire> */
    public function stagiaires(): HasMany
    {
        return $this->hasMany(Stagiaire::class);
    }

    /** @return HasMany<Affectation> */
    public function affectations(): HasMany
    {
        return $this->hasMany(Affectation::class);
    }

    /**
     * Per-module progress tracking for this groupe (hours realized vs
     * planned). Kept in sync automatically by AvancementService whenever
     * a Seance is created/deleted for one of this groupe's affectations.
     *
     * @return HasMany<Avancement>
     */
    public function avancements(): HasMany
    {
        return $this->hasMany(Avancement::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeResidentiel(Builder $query): Builder
    {
        return $query->where('mode', 'Résidentiel');
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('code', 'like', "%{$term}%");
    }
}
