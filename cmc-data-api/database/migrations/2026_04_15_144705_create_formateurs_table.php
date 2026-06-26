<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formateurs', function (Blueprint $table) {
            $table->string('mle', 32)->primary();
            $table->foreignId('pole_id')->constrained('poles')->restrictOnDelete();
            $table->string('nom_prenom');
            $table->string('statut')->nullable();
            $table->string('email_edu')->nullable()->unique();
            $table->decimal('mhs', 8, 2)->default(26);

            // EFP Mutualisé: the pole the trainer actually teaches in, when different
            // from their home `pole_id` (Affectation column in Base_Formateurs.xlsx).
            $table->string('efp_mutualise')->nullable();
            $table->boolean('mutualise')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['pole_id', 'nom_prenom']);
            $table->index(['mutualise']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formateurs');
    }
};
