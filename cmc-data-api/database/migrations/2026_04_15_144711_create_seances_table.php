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
        Schema::create('seances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affectation_id')->constrained('affectations')->cascadeOnDelete();
            $table->string('type', 32)->default('cours');
            $table->date('date')->index();
            $table->foreignId('time_range_id')->constrained('time_ranges')->restrictOnDelete();
            $table->timestamps();

            $table->index(['affectation_id', 'type']);
            $table->index(['date', 'time_range_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seances');
    }
};
