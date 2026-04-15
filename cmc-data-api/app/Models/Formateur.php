<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Formateur extends Model
{
    protected $primaryKey = 'mle';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['mle', 'pole_id', 'nom_prenom', 'statut', 'email_edu', 'mhs'];

    protected $casts = [
        'mhs' => 'decimal:2',
    ];

    /** @return BelongsTo<Pole, Formateur> */
    public function pole(): BelongsTo
    {
        return $this->belongsTo(Pole::class);
    }

    /** @return HasMany<Affectation> */
    public function affectations(): HasMany
    {
        return $this->hasMany(Affectation::class, 'formateur_mle', 'mle');
    }
}
