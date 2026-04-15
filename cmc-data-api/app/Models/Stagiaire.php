<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stagiaire extends Model
{
    protected $primaryKey = 'cef';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['cef', 'groupe_id', 'cni', 'nom', 'prenom', 'date_naissance', 'genre'];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    /** @return BelongsTo<Groupe, Stagiaire> */
    public function groupe(): BelongsTo
    {
        return $this->belongsTo(Groupe::class);
    }

    /** @return HasMany<Note> */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class, 'stagiaire_cef', 'cef');
    }
}
