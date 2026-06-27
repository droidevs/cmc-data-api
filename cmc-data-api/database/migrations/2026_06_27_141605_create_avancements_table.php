<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Avancement tracks, per (Groupe, Module) pair, how many hours have
 * actually been delivered ("réalisées") against the module's planned
 * hours — mirroring AvancementProgramme.xlsx "MH Réalisée Présentiel" /
 * "MH Réalisée Sync" / "MH Réalisée Globale" / "Taux Réalisation" columns.
 *
 * Unlike Affectation (which links Groupe + Module + Formateur and carries
 * the *assigned* hours), Avancement is keyed on (Groupe, Module) only and
 * carries the *realized* hours, kept in sync automatically whenever a
 * Seance is created/updated/deleted for the corresponding Affectation
 * (see App\Services\AvancementService and the Seance observer wiring in
 * AppServiceProvider).
 *
 * taux_realisation_* columns are persisted (not purely virtual) so that
 * historical realization rates remain stable even if the underlying
 * module's planned hours are edited later — exactly like the frozen
 * "Taux Réalisation" snapshot columns in the source spreadsheet.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avancements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groupe_id')->constrained('groupes')->cascadeOnDelete();

            // References modules.id (surrogate key) — same rationale as
            // affectations.module_id: code_module alone isn't unique
            // across annees, so the FK must target the surrogate key.
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();

            // Realized hours, auto-maintained from Seance create/delete.
            $table->decimal('mh_realisee_presentiel', 8, 2)->default(0);
            $table->decimal('mh_realisee_syn', 8, 2)->default(0);
            $table->decimal('mh_realisee_globale', 8, 2)->default(0);

            // Realization rate vs the module's planned hours (percentage,
            // 0-100+, can exceed 100 if more hours were delivered than
            // planned — matches the source data, which has rows >100%).
            $table->decimal('taux_realisation_presentiel', 6, 2)->default(0);
            $table->decimal('taux_realisation_syn', 6, 2)->default(0);
            $table->decimal('taux_realisation_globale', 6, 2)->default(0);

            $table->timestamps();

            // One avancement row per (groupe, module) — repeated seances
            // accumulate into the same row rather than creating duplicates.
            $table->unique(['groupe_id', 'module_id'], 'avancements_groupe_module_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avancements');
    }
};
