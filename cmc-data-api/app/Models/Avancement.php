<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Avancement (progress tracker) for a (Groupe, Module) pair.
 *
 * Mirrors AvancementProgramme.xlsx "MH Réalisée Présentiel" / "MH Réalisée
 * Sync" / "MH Réalisée Globale" / "Taux Réalisation Présentiel" / "Taux
 * Réalisation Syn" / "Taux Réalisation (P & SYN)" columns.
 *
 * Unlike Affectation (Groupe + Module + Formateur, *assigned* hours),
 * Avancement is keyed on (Groupe, Module) only and tracks *realized*
 * hours — i.e. how much of the module has actually been taught to that
 * groupe so far. It is kept in sync automatically: every time a Seance is
 * created or deleted for an Affectation belonging to this (groupe, module)
 * pair, AvancementService recomputes the realized hours and realization
 * rates from the TimeRange duration of that groupe's séances.
 *
 * Realization rate = realized hours / module's planned hours (Module::mh_*),
 * expressed as a percentage. Can exceed 100 if more hours were delivered
 * than planned (this happens in the real dataset).
 */
class Avancement extends Model
{
    use HasFactory;

    protected $fillable = [
        'groupe_id',
        'module_id',
        'mh_realisee_presentiel',
        'mh_realisee_syn',
        'mh_realisee_globale',
        'taux_realisation_presentiel',
        'taux_realisation_syn',
        'taux_realisation_globale',
    ];

    protected $casts = [
        'mh_realisee_presentiel'      => 'decimal:2',
        'mh_realisee_syn'             => 'decimal:2',
        'mh_realisee_globale'         => 'decimal:2',
        'taux_realisation_presentiel' => 'decimal:2',
        'taux_realisation_syn'        => 'decimal:2',
        'taux_realisation_globale'    => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    /** @return BelongsTo<Groupe, Avancement> */
    public function groupe(): BelongsTo
    {
        return $this->belongsTo(Groupe::class);
    }

    /** @return BelongsTo<Module, Avancement> */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id', 'id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForGroupe(Builder $query, int $groupeId): Builder
    {
        return $query->where('groupe_id', $groupeId);
    }

    public function scopeForModule(Builder $query, string $moduleId): Builder
    {
        return $query->where('module_id', $moduleId);
    }

    /** Avancements whose global realization rate is below 100%. */
    public function scopeEnCours(Builder $query): Builder
    {
        return $query->where('taux_realisation_globale', '<', 100);
    }

    /** Avancements whose global realization rate has reached/exceeded 100%. */
    public function scopeTermine(Builder $query): Builder
    {
        return $query->where('taux_realisation_globale', '>=', 100);
    }
}
