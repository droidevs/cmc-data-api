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
        Schema::create('annees', function (Blueprint $table) {
            $table->id();
            $table->string('libelle')->index();
            $table->string('filiere_code', 32);
            $table->timestamps();

            $table->foreign('filiere_code')->references('code_filiere')->on('filieres')->cascadeOnDelete();
            $table->unique(['filiere_code', 'libelle']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('annees');
    }
};
