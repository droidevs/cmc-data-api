<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pole extends Model
{
    use HasFactory;

    protected $fillable = ['libelle'];

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @return HasMany<Espace> */
    public function espaces(): HasMany
    {
        return $this->hasMany(Espace::class);
    }

    /** @return HasMany<Formateur> */
    public function formateurs(): HasMany
    {
        return $this->hasMany(Formateur::class);
    }

    /** @return HasMany<Filiere> */
    public function filieres(): HasMany
    {
        return $this->hasMany(Filiere::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('libelle', 'like', "%{$term}%");
    }
}
