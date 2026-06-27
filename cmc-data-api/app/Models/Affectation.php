<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AffectationMode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Affectation (module assignment) sourced from AvancementProgramme.xlsx.
 *
 * Excel mapping:
 *   Groupe                         → groupe_id
 *   Code Module                    → module_code
 *   Mle Affecté Présentiel Actif   → formateur_mle       (presentiel trainer)
 *   Mle Affecté Syn Actif          → formateur_mle_syn   ← NEW (synchronous/online trainer)
 *   Mode                           → mode
 *   MH Affectée Présentiel         → mh_affecte          (presentiel hours)
 *   MH Affectée Sync               → mh_affecte_syn      ← NEW (sync hours)
 *
 * The two trainers (presentiel vs syn) can be different people.
 */
class Affectation extends Model
{
    protected $fillable = [
        'groupe_id',
        'module_id',
        'formateur_mle',        // trainer for presentiel sessions
        'formateur_mle_syn',    // ← NEW: trainer for synchronous sessions
        'mode',
        'mh_affecte',           // hours assigned for presentiel
        'mh_affecte_syn',       // ← NEW: hours assigned for sync
    ];

    protected $casts = [
        'mh_affecte'     => 'decimal:2',
        'mh_affecte_syn' => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @return BelongsTo<Groupe, Affectation> */
    public function groupe(): BelongsTo
    {
        return $this->belongsTo(Groupe::class);
    }

    /** @return BelongsTo<Module, Affectation> */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /** Trainer for in-person (présentiel) sessions. */
    /** @return BelongsTo<Formateur, Affectation> */
    public function formateur(): BelongsTo
    {
        return $this->belongsTo(Formateur::class, 'formateur_mle', 'mle');
    }

    /** Trainer for synchronous (online) sessions. */
    /** @return BelongsTo<Formateur, Affectation> */
    public function formateurSyn(): BelongsTo
    {
        return $this->belongsTo(Formateur::class, 'formateur_mle_syn', 'mle');
    }

    /** @return HasMany<Seance> */
    public function seances(): HasMany
    {
        return $this->hasMany(Seance::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeByFormateur(Builder $query, string $mle): Builder
    {
        return $query->where('formateur_mle', $mle)
            ->orWhere('formateur_mle_syn', $mle);
    }

    public function scopeWithHours(Builder $query): Builder
    {
        return $query->whereNotNull('mh_affecte');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    /** Total MH (présentiel + sync) for this affectation. */
    public function getMhTotaleAttribute(): float
    {
        return (float) $this->mh_affecte + (float) $this->mh_affecte_syn;
    }

    public function getModuleCodeAttribute(): ?string
    {
        return $this->module?->code_module;
    }
}
