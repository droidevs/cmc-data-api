<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeRange extends Model
{
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
}
