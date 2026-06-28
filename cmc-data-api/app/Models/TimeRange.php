<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * TimeRange — a named teaching slot (e.g. 08:30–11:00).
 *
 * start_time and end_time are stored as TIME strings in the database.
 * They are kept as plain strings (not Carbon instances) to avoid
 * phantom-date injection that occurs when casting a TIME column to
 * `datetime`. Duration is computed directly from the HH:MM strings.
 */
class TimeRange extends Model
{
    use HasFactory;

    protected $fillable = ['start_time', 'end_time'];

    protected function casts(): array
    {
        return [
            'start_time' => 'string',
            'end_time'   => 'string',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @return HasMany<Seance> */
    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /**
     * Duration of this slot in hours (e.g. "08:30" – "11:00" → 2.5).
     * Used by AvancementService to convert a Seance into realized hours.
     * Computed from raw TIME strings to avoid Carbon day-boundary issues.
     */
    protected function durationHours(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                if (! $this->start_time || ! $this->end_time) {
                    return 0.0;
                }

                [$startH, $startM] = array_map('intval', explode(':', $this->start_time));
                [$endH, $endM]     = array_map('intval', explode(':', $this->end_time));

                $minutes = ($endH * 60 + $endM) - ($startH * 60 + $startM);

                return round($minutes / 60, 2);
            },
        );
    }
}
