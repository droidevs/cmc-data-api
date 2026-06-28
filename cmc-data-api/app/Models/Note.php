<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NoteType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Note (grade) for one stagiaire on one séance.
 *
 * `type` mirrors the parent Seance's `type` and must always match it
 * (enforced in StoreNoteRequest / UpdateNoteRequest, and re-asserted in
 * NoteService::create()/update() as a defence-in-depth check).
 *
 * Only "evaluable" séance types carry a grade — a plain "cours" séance
 * cannot have a Note. Use NoteType::evaluable() as the single source of
 * truth for which types qualify.
 *
 * @property NoteType $type
 */
class Note extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['seance_id', 'stagiaire_cef', 'valeur', 'type', 'decision'];

    protected function casts(): array
    {
        return [
            'type'   => NoteType::class,
            'valeur' => 'decimal:2',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

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
