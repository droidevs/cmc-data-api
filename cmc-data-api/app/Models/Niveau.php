<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Niveau extends Model
{
    use HasFactory;

    protected $fillable = ['libelle'];

    /** @return HasMany<Filiere> */
    public function filieres(): HasMany
    {
        return $this->hasMany(Filiere::class);
    }
}
