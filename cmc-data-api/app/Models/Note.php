<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    protected $fillable = ['seance_id', 'stagiaire_cef', 'valeur', 'type', 'decision'];

    protected $casts = [
        'valeur' => 'decimal:2',
    ];

    /** @return BelongsTo<Seance, Note> */
    public function seance(): BelongsTo
    {
        return $this->belongsTo(Seance::class);
    }

    /** @return BelongsTo<Stagiaire, Note> */
    public function stagiaire(): BelongsTo
    {
        return $this->belongsTo(Stagiaire::class, 'stagiaire_cef', 'cef');
    }
}
