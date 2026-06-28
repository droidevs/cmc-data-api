<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeRange extends Model
{
    use HasFactory;

    protected $fillable = ['start_time', 'end_time'];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /** @return HasMany<Seance> */
    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }

    /**
     * Duration of this slot in hours (e.g. 08:30–11:00 → 2.5).
     * Used by AvancementService to convert a Seance into realized hours.
     */
    public function getDurationHoursAttribute(): float
    {
        if (! $this->start_time || ! $this->end_time) {
            return 0.0;
        }

        $minutes = $this->end_time->diffInMinutes($this->start_time);

        return round($minutes / 60, 2);
    }
}
