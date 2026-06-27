<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            // Surrogate key. `code_module` (e.g. "M101") is NOT globally unique in the
            // source data: the same code is reused across different filieres/annees
            // with a different `libelle` each time (verified against AvancementProgramme.xlsx —
            // e.g. "EGTS204" = "Culture entrepreneuriale" in one filiere's 2nd year but a
            // different subject in another). The real natural key is (code_module, annee_id).
            $table->id();
            $table->string('code_module', 32)->index();
            $table->foreignId('annee_id')->constrained('annees')->cascadeOnDelete();
            $table->string('libelle');
            $table->boolean('regional')->default(false);
            $table->decimal('mh_presentiel', 8, 2)->default(0);
            $table->decimal('mh_syn', 8, 2)->default(0);
            $table->decimal('mh_asyn', 8, 2)->default(0);
            $table->decimal('mh_totale', 8, 2)->default(0);
            $table->timestamps();

            $table->unique(['code_module', 'annee_id']);
            $table->index(['annee_id', 'libelle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
