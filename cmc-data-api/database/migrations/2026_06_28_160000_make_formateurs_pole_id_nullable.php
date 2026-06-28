<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Make formateurs.pole_id nullable so that trainers can be imported
 * even when the Affectation / pole column is absent in the Excel file.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formateurs', function (Blueprint $table) {
            // Drop the existing FK first, then redefine as nullable
            $table->dropForeign(['pole_id']);
            $table->foreignId('pole_id')->nullable()->change()->constrained('poles')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('formateurs', function (Blueprint $table) {
            $table->dropForeign(['pole_id']);
            $table->foreignId('pole_id')->nullable(false)->change()->constrained('poles')->restrictOnDelete();
        });
    }
};
