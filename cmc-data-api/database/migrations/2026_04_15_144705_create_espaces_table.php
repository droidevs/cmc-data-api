<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('espaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pole_id')->constrained('poles')->cascadeOnDelete();
            $table->string('libelle');
            $table->unsignedInteger('capacite')->nullable();
            $table->timestamps();

            // A room name only needs to be unique within its own pole.
            $table->unique(['pole_id', 'libelle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('espaces');
    }
};
