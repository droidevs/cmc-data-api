<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Espace (physical room / lab / workshop) belonging to a Pole.
 *
 * A Seance (teaching session) optionally takes place in one Espace.
 * `espace_id` on `seances` is nullable because synchronous/online seances
 * have no physical room.
 */
class Espace extends Model
{
    protected $fillable = [
        'pole_id',
        'libelle',
        'capacite',
    ];

    protected $casts = [
        'capacite' => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @return BelongsTo<Pole, Espace> */
    public function pole(): BelongsTo
    {
        return $this->belongsTo(Pole::class);
    }

    /** @return HasMany<Seance> */
    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('libelle', 'like', "%{$term}%");
    }

    /** Espaces with enough capacite to host a group of the given size. */
    public function scopeWithCapacityFor(Builder $query, int $effectif): Builder
    {
        return $query->where(function (Builder $q) use ($effectif) {
            $q->whereNull('capacite')->orWhere('capacite', '>=', $effectif);
        });
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Whether this espace is free for the given date and time range
     * (i.e. not already booked by another seance).
     */
    public function isAvailable(string $date, int $timeRangeId, ?int $excludingSeanceId = null): bool
    {
        return ! $this->seances()
            ->where('date', $date)
            ->where('time_range_id', $timeRangeId)
            ->when($excludingSeanceId, fn (Builder $q) => $q->whereKeyNot($excludingSeanceId))
            ->exists();
    }
}
