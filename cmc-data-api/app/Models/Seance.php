<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seance extends Model
{
    protected $fillable = ['affectation_id', 'espace_id', 'type', 'date', 'time_range_id'];

    protected $casts = [
        'date' => 'date',
    ];

    public function espace(): BelongsTo
    {
        return $this->belongsTo(Espace::class);
    }

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

    /**
     * Whether this séance's type is an evaluable one (i.e. notes are
     * allowed). A plain "cours" séance never carries a grade — only
     * cc | efm | exam séances do. Mirrors NoteService::assertEvaluable()
     * and is exposed here so views/forms can check it without duplicating
     * the vocabulary.
     */
    public function getIsEvaluableAttribute(): bool
    {
        return in_array($this->type, Note::EVALUABLE_SEANCE_TYPES, true);
    }
}
