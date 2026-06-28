<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeFormation extends Model
{
    use HasFactory;

    protected $fillable = ['libelle'];

    /** @return HasMany<Filiere> */
    public function filieres(): HasMany
    {
        return $this->hasMany(Filiere::class);
    }
}
