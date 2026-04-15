<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeRange extends Model
{
    protected $fillable = ['start_time', 'end_time'];

    /** @return HasMany<Seance> */
    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }
}

