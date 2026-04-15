<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('affectations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('groupe_id')->constrained('groupes')->cascadeOnDelete();
            $table->string('module_code', 32);
            $table->string('formateur_mle', 32);
            $table->string('mode', 32)->default('presentiel');
            $table->decimal('mh_affecte', 8, 2)->default(0);
            $table->timestamps();

            $table->foreign('module_code')->references('code_module')->on('modules')->restrictOnDelete();
            $table->foreign('formateur_mle')->references('mle')->on('formateurs')->restrictOnDelete();

            $table->unique(['groupe_id', 'module_code', 'formateur_mle'], 'affectations_unique_triplet');
            $table->index(['groupe_id', 'module_code']);
            $table->index(['formateur_mle']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affectations');
    }
};
