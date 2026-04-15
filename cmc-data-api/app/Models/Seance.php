<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seance extends Model
{
    protected $fillable = ['affectation_id', 'type', 'date', 'time_range_id'];

    protected $casts = [
        'date' => 'date',
    ];

    /** @return BelongsTo<Affectation, Seance> */
    public function affectation(): BelongsTo
    {
        return $this->belongsTo(Affectation::class);
    }

    /** @return BelongsTo<TimeRange, Seance> */
    public function timeRange(): BelongsTo
    {
        return $this->belongsTo(TimeRange::class);
    }

    /** @return HasMany<Note> */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
