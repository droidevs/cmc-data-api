<?php

namespace App\Models;

use App\Enums\NoteType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Note (grade) for one stagiaire on one séance.
 *
 * `type` is no longer an independently-chosen value — it must always
 * equal the parent Seance's `type` (enforced in StoreNoteRequest /
 * UpdateNoteRequest, and re-asserted in NoteService::create()/update() as
 * a defence-in-depth check, since a Note only makes sense in the context
 * of the séance that produced it).
 *
 * Only "evaluable" séance types actually carry a grade — a plain "cours"
 * séance is a regular class session with nothing to assess, so creating a
 * Note against a "cours" séance is rejected. EVALUABLE_SEANCE_TYPES is the
 * single source of truth for which types qualify; both the Seance model
 * (Seance::is_evaluable) and the request validation classes read from it.
 */
class Note extends Model
{
    /** Séance types that may carry a Note. A plain "cours" séance cannot. */
    public const EVALUABLE_SEANCE_TYPES = [
        NoteType::CC,
        NoteType::EFM
    ];

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
