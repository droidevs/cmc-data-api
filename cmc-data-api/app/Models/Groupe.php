<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Groupe (class group) sourced from AvancementProgramme.xlsx.
 *
 * Excel mapping:
 *   Groupe           → code       (e.g. "DEV101", "DEVOWFS201")
 *   Effectif Groupe  → effectif
 *   Mode             → mode       ← NEW: "Résidentiel" | "Alternance" …
 *   Code Filière     → annee.filiere_code
 *   Année de formation → annee.libelle
 */
class Groupe extends Model
{
    protected $fillable = [
        'annee_id',
        'code',
        'effectif',
        'mode',   // ← NEW: mode de formation (Résidentiel / Alternance)
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
