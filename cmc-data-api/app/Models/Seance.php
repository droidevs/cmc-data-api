<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NoteType;
use App\Enums\SeanceType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Seance (teaching session) linked to one Affectation.
 *
 * @property SeanceType $type
 */
class Seance extends Model
{
    use HasFactory;

    protected $fillable = ['affectation_id', 'espace_id', 'type', 'date', 'time_range_id'];

    protected function casts(): array
    {
        return [
            'type' => SeanceType::class,
            'date' => 'date',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @return BelongsTo<Espace, Seance> */
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

    // ─── Accessors ────────────────────────────────────────────────────────────

    /**
     * Whether this séance's type allows notes. A plain "cours" séance never
     * carries a grade — only cc | efm séances do.
     * Delegates to NoteType::evaluable() as the single source of truth.
     */
    protected function isEvaluable(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->type?->value, NoteType::evaluable(), true),
        );
    }
}
