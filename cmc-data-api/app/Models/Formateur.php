<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FormateurStatut;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Formateur (trainer) sourced from Base_Formateurs.xlsx.
 *
 * Key fields from the spreadsheet:
 *   Mle            → mle   (matricule, string PK e.g. "19307", "H418299")
 *   Nom et Prénom  → nom_prenom
 *   MHS            → mhs   (monthly teaching hours, default 26)
 *   Statut         → statut (OFPPT | Vacataire | …)
 *   Affectation    → pole home (derived via pole_id)
 *   EFP Mutualisé → efp_mutualise (the CMC pole they actually teach in)
 *   Mutualisé      → mutualise (boolean)
 *   Email Edu      → email_edu
 */
class Formateur extends Model
{
    protected $primaryKey = 'mle';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'mle',
        'pole_id',
        'nom_prenom',
        'statut',
        'email_edu',
        'mhs',
        'efp_mutualise',  // ← NEW: the actual EFP they teach in (mutualisé)
        'mutualise',      // ← NEW: boolean flag
    ];

    protected $casts = [
        'mhs'       => 'decimal:2',
        'mutualise' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @return BelongsTo<Pole, Formateur> */
    public function pole(): BelongsTo
    {
        return $this->belongsTo(Pole::class);
    }

    /** @return HasMany<Affectation> */
    public function affectations(): HasMany
    {
        return $this->hasMany(Affectation::class, 'formateur_mle', 'mle');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeOfppt(Builder $query): Builder
    {
        return $query->where('statut', 'OFPPT');
    }

    public function scopeMutualise(Builder $query): Builder
    {
        return $query->where('mutualise', true);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('nom_prenom', 'like', "%{$term}%")
            ->orWhere('mle', 'like', "%{$term}%");
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /** Total MH assigned across all affectations. */
    public function getMhAffecteeAttribute(): float
    {
        return (float) $this->affectations()->sum('mh_affecte');
    }
}
