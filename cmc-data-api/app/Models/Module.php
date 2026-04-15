<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected $primaryKey = 'code_module';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['code_module', 'annee_id', 'libelle'];

    /** @return BelongsTo<Annee, Module> */
    public function annee(): BelongsTo
    {
        return $this->belongsTo(Annee::class);
    }

    /** @return HasMany<Affectation> */
    public function affectations(): HasMany
    {
        return $this->hasMany(Affectation::class, 'module_code', 'code_module');
    }
}
