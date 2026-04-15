<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Seance extends Model
{
    protected $fillable = ['affectation_id', 'type'];

    /** @return BelongsTo<Affectation, Seance> */
    public function affectation(): BelongsTo
    {
        return $this->belongsTo(Affectation::class);
    }

    /** @return HasOne<DateSeance> */
    public function dateSeance(): HasOne
    {
        return $this->hasOne(DateSeance::class);
    }

    /** @return HasMany<Note> */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
