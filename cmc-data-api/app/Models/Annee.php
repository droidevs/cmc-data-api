<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Annee extends Model
{
    protected $fillable = ['filiere_code', 'libelle'];

    /** @return BelongsTo<Filiere, Annee> */
    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class, 'filiere_code', 'code_filiere');
    }

    /** @return HasMany<Groupe> */
    public function groupes(): HasMany
    {
        return $this->hasMany(Groupe::class);
    }

    /** @return HasMany<Module> */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class);
    }
}
