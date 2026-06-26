<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Stagiaire (trainee / student) sourced from lister_minimized.xlsx
 * and BasePlateEvaluation.xlsx.
 *
 * Excel mapping:
 *   MatriculeEtudiant → cef         (PK, string)
 *   CIN               → cni
 *   Nom               → nom
 *   Prenom            → prenom
 *   Nom_Arabe         → nom_arabe   ← NEW
 *   Prenom_arabe      → prenom_arabe← NEW
 *   Sexe              → genre       (F / H)
 *   DateNaissance     → date_naissance
 *   CodeDiplome       → groupe_id   (via groupe lookup)
 *   NTelelephone      → telephone   ← NEW
 *   Adresse           → adresse     ← NEW
 *   NiveauScolaire    → niveau_scolaire ← NEW
 *   EtudiantActif     → actif       ← NEW (boolean)
 */
class Stagiaire extends Model
{
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
        'telephone',       // ← NTelelephone
        'adresse',
        'niveau_scolaire',
        'actif',           // ← EtudiantActif boolean
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'actif'          => 'boolean',
    ];

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

    public function scopeByGenre(Builder $query, string $genre): Builder
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

    public function getNomCompletAttribute(): string
    {
        return "{$this->nom} {$this->prenom}";
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_naissance?->age;
    }
}
