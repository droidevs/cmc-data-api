<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affectations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groupe_id')->constrained('groupes')->cascadeOnDelete();

            // References modules.id (surrogate key), NOT the human-readable code_module,
            // since code_module alone is not unique across annees.
            $table->foreignId('module_id')->constrained('modules')->restrictOnDelete();

            $table->string('formateur_mle', 32);
            $table->string('formateur_mle_syn', 32)->nullable();
            $table->string('mode', 32)->default('presentiel');
            $table->decimal('mh_affecte', 8, 2)->default(0);
            $table->decimal('mh_affecte_syn', 8, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('formateur_mle')->references('mle')->on('formateurs')->restrictOnDelete();
            $table->foreign('formateur_mle_syn')->references('mle')->on('formateurs')->restrictOnDelete();

            // A présentiel affectation and a synchrone affectation can legitimately
            // coexist for the same groupe+module+trainer (verified in AvancementProgramme.xlsx:
            // the same Mle frequently appears as both présentiel and syn trainer on one row),
            // so `mode` is part of the uniqueness rule rather than being orthogonal to it.
            $table->unique(['groupe_id', 'module_id', 'formateur_mle', 'mode'], 'affectations_unique_assignment');
            $table->index(['groupe_id', 'module_id']);
            $table->index(['formateur_mle']);
            $table->index(['formateur_mle_syn']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affectations');
    }
};
