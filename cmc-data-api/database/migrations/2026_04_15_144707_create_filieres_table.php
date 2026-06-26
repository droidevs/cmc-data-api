<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('filieres', function (Blueprint $table) {
            $table->string('code_filiere', 32)->primary();
            $table->foreignId('pole_id')->constrained('poles')->restrictOnDelete();
            $table->foreignId('niveau_id')->constrained('niveaux')->restrictOnDelete();
            $table->foreignId('type_formation_id')->constrained('type_formations')->restrictOnDelete();
            $table->string('libelle');
            $table->string('secteur')->nullable()->index();
            $table->timestamps();

            $table->index(['pole_id', 'libelle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('filieres');
    }
};
