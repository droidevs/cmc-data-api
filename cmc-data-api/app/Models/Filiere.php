<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Filiere (programme / speciality) sourced from AvancementProgramme.xlsx.
 *
 * Excel mapping:
 *   Code Filière      → code_filiere  (PK, string e.g. "DIA_DEV_TS")
 *   filière           → libelle
 *   Type de formation → type_formation_id
 *   Niveau            → niveau_id     (TS, T, Q, FQ …)
 *   Secteur           → secteur       e.g. "Digital et Intelligence Artificielle"
 *   Pôle              → pole_id
 */
class Filiere extends Model
{
    use HasFactory;

    protected $primaryKey = 'code_filiere';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'code_filiere',
        'pole_id',
        'niveau_id',
        'type_formation_id',
        'libelle',
        'secteur',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @return BelongsTo<Pole, Filiere> */
    public function pole(): BelongsTo
    {
        return $this->belongsTo(Pole::class);
    }

    /** @return BelongsTo<Niveau, Filiere> */
    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    /** @return BelongsTo<TypeFormation, Filiere> */
    public function typeFormation(): BelongsTo
    {
        return $this->belongsTo(TypeFormation::class);
    }

    /** @return HasMany<Annee> */
    public function annees(): HasMany
    {
        return $this->hasMany(Annee::class, 'filiere_code', 'code_filiere');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeBySecteur(Builder $query, string $secteur): Builder
    {
        return $query->where('secteur', $secteur);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('libelle', 'like', "%{$term}%")
            ->orWhere('code_filiere', 'like', "%{$term}%");
    }
}
