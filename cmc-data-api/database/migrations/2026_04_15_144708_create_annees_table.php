<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annees', function (Blueprint $table) {
            $table->id();
            // Human-readable label, e.g. "1ère année - Tronc Commun"
            // or "2ème année - Option Développement Web Full Stack".
            $table->string('libelle');
            $table->string('filiere_code', 32);
            $table->timestamps();

            $table->foreign('filiere_code')->references('code_filiere')->on('filieres')->cascadeOnDelete();
            $table->unique(['filiere_code', 'libelle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annees');
    }
};
