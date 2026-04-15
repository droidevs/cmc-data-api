<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DateSeance extends Model
{
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
