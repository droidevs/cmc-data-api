<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Affectation extends Model
{
    protected $fillable = ['groupe_id', 'module_code', 'formateur_mle', 'mode', 'mh_affecte'];

    protected $casts = [
        'mh_affecte' => 'decimal:2',
    ];

    /** @return BelongsTo<Groupe, Affectation> */
    public function groupe(): BelongsTo
    {
        return $this->belongsTo(Groupe::class);
    }

    /** @return BelongsTo<Module, Affectation> */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_code', 'code_module');
    }

    /** @return BelongsTo<Formateur, Affectation> */
    public function formateur(): BelongsTo
    {
        return $this->belongsTo(Formateur::class, 'formateur_mle', 'mle');
    }

    /** @return HasMany<Seance> */
    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }
}
