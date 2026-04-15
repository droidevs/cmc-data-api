<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DateSeance extends Model
{
    /**
     * Legacy model: `date_seances` table was removed (not deployed yet).
     * Scheduling is now represented by `seances.date` + `seances.time_range_id`.
     *
     * Keep this file only to avoid breaking older imports; do not use it.
     */

    protected $table = 'date_seances';

    protected $fillable = ['seance_id', 'date'];

    protected $casts = [
        'date' => 'datetime',
    ];

    /** @return BelongsTo<Seance, DateSeance> */
    public function seance(): BelongsTo
    {
        return $this->belongsTo(Seance::class);
    }
}
