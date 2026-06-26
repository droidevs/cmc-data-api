<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('groupes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annee_id')->constrained('annees')->cascadeOnDelete();
            $table->string('code')->index();
            $table->unsignedInteger('effectif')->default(0);
            $table->string('mode')->nullable();
            $table->timestamps();

            $table->unique(['annee_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('groupes');
    }
};
