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
        Schema::create('formateurs', function (Blueprint $table) {
            $table->string('mle', 32)->primary();
            $table->foreignId('pole_id')->constrained('poles')->cascadeOnDelete();
            $table->string('nom_prenom');
            $table->string('statut')->nullable();
            $table->string('email_edu')->nullable()->index();
            $table->decimal('mhs', 8, 2)->default(0);
            $table->timestamps();

            $table->index(['pole_id', 'nom_prenom']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formateurs');
    }
};
