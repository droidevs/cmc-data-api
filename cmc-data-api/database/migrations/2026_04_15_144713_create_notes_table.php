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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seance_id')->constrained('seances')->cascadeOnDelete();
            $table->string('stagiaire_cef', 32);
            $table->decimal('valeur', 5, 2)->nullable();
            $table->string('type', 32)->default('cc');
            $table->string('decision', 64)->nullable();
            $table->timestamps();

            $table->foreign('stagiaire_cef')->references('cef')->on('stagiaires')->cascadeOnDelete();
            $table->unique(['seance_id', 'stagiaire_cef', 'type']);
            $table->index(['stagiaire_cef']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
