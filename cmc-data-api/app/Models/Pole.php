<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pole extends Model
{
    protected $fillable = ['libelle'];

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
}
