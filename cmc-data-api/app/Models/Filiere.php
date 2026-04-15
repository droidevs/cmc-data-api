<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filiere extends Model
{
    protected $primaryKey = 'code_filiere';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['code_filiere', 'pole_id', 'niveau_id', 'type_formation_id', 'libelle'];

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
}
