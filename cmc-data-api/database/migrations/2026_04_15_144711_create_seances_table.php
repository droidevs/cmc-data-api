<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affectation_id')->constrained('affectations')->cascadeOnDelete();

            // NEW: physical room/space the session takes place in. Nullable because
            // a "synchrone" (online) seance has no physical room.
            $table->foreignId('espace_id')->nullable()->constrained('espaces')->nullOnDelete();

            $table->enum("type", array_column(\App\Enums\SeanceType::cases(), 'value'))->default(\App\Enums\SeanceType::COURS->value);
            $table->date('date')->index();
            $table->foreignId('time_range_id')->constrained('time_ranges')->restrictOnDelete();
            $table->timestamps();

            $table->index(['affectation_id', 'type']);
            $table->index(['date', 'time_range_id']);

            // A single room cannot legitimately host two seances at the same
            // date + time slot — this is the core scheduling-conflict guard.
            $table->unique(['espace_id', 'date', 'time_range_id'], 'seances_no_double_booking');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seances');
    }
};
