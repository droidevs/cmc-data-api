<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Genre;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Stagiaire (trainee / student) sourced from lister_minimized.xlsx
 * and BasePlateEvaluation.xlsx.
 *
 * Excel mapping:
 *   MatriculeEtudiant → cef            (PK, string)
 *   CIN               → cni
 *   Nom               → nom
 *   Prenom            → prenom
 *   Nom_Arabe         → nom_arabe
 *   Prenom_arabe      → prenom_arabe
 *   Sexe              → genre          (Genre enum: F / H)
 *   DateNaissance     → date_naissance
 *   CodeDiplome       → groupe_id      (via groupe lookup)
 *   NTelelephone      → telephone
 *   Adresse           → adresse
 *   NiveauScolaire    → niveau_scolaire
 *   EtudiantActif     → actif          (boolean)
 *
 * @property Genre|null $genre
 */
class Stagiaire extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'cef';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'cef',
        'groupe_id',
        'cni',
        'nom',
        'prenom',
        'nom_arabe',
        'prenom_arabe',
        'date_naissance',
        'genre',
        'telephone',
        'adresse',
        'niveau_scolaire',
        'actif',
    ];

    /** PII fields excluded from default serialization. */
    protected $hidden = [
        'cni',
        'telephone',
        'adresse',
        'date_naissance',
    ];

    protected function casts(): array
    {
        return [
            'genre'          => Genre::class,
            'date_naissance' => 'date',
            'actif'          => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @return BelongsTo<Groupe, Stagiaire> */
    public function groupe(): BelongsTo
    {
        return $this->belongsTo(Groupe::class);
    }

    /** @return HasMany<Note> */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'stagiaire_cef', 'cef');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActif(Builder $query): Builder
    {
        return $query->where('actif', true);
    }

    public function scopeByGenre(Builder $query, Genre $genre): Builder
    {
        return $query->where('genre', $genre);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('nom', 'like', "%{$term}%")
            ->orWhere('prenom', 'like', "%{$term}%")
            ->orWhere('cef', 'like', "%{$term}%")
            ->orWhere('cni', 'like', "%{$term}%");
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    protected function nomComplet(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->nom} {$this->prenom}",
        );
    }

    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn (): ?int => $this->date_naissance?->age,
        );
    }
}
