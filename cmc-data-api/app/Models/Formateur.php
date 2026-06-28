<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FormateurStatut;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Formateur (trainer) sourced from Base_Formateurs.xlsx.
 *
 * Key fields from the spreadsheet:
 *   Mle            → mle          (matricule, string PK e.g. "19307", "H418299")
 *   Nom et Prénom  → nom_prenom
 *   MHS            → mhs          (monthly teaching hours, default 26)
 *   Statut         → statut       (FormateurStatut enum)
 *   Affectation    → pole home    (derived via pole_id)
 *   EFP Mutualisé  → efp_mutualise (the CMC pole they actually teach in)
 *   Mutualisé      → mutualise    (boolean)
 *   Email Edu      → email_edu
 *
 * @property FormateurStatut|null $statut
 */
class Formateur extends Model
{
    use HasFactory, SoftDeletes;

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
        'efp_mutualise',
        'mutualise',
    ];

    protected function casts(): array
    {
        return [
            'statut'    => FormateurStatut::class,
            'mhs'       => 'decimal:2',
            'mutualise' => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @return BelongsTo<Pole, Formateur> */
    public function pole(): BelongsTo
    {
        return $this->belongsTo(Pole::class);
    }

    /** Affectations where this trainer is the présentiel trainer.
     *
     * @return HasMany<Affectation>
     */
    public function affectations(): HasMany
    {
        return $this->hasMany(Affectation::class, 'formateur_mle', 'mle');
    }

    /** Affectations where this trainer is the synchronous trainer.
     *
     * @return HasMany<Affectation>
     */
    public function affectationsSyn(): HasMany
    {
        return $this->hasMany(Affectation::class, 'formateur_mle_syn', 'mle');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeOfppt(Builder $query): Builder
    {
        return $query->where('statut', FormateurStatut::Ofppt);
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

    // ─── Performance Note ─────────────────────────────────────────────────────

    /**
     * To get total assigned hours per formateur WITHOUT triggering an N+1,
     * use withSum() in the query rather than an accessor:
     *
     *   Formateur::withSum('affectations as mh_affectee', 'mh_affecte')->get();
     *
     * Then access via: $formateur->mh_affectee
     */
}
