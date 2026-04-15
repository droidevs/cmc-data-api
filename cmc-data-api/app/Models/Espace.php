<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Espace extends Model
{
    protected $fillable = ['pole_id', 'libelle'];

    /** @return BelongsTo<Pole, Espace> */
    public function pole(): BelongsTo
    {
        return $this->belongsTo(Pole::class);
    }
}
