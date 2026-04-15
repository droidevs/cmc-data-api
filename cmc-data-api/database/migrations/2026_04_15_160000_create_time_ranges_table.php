<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('time_ranges', function (Blueprint $table) {
			$table->id();
			$table->time('start_time');
			$table->time('end_time');
			$table->timestamps();

			$table->unique(['start_time', 'end_time']);
			$table->index(['start_time', 'end_time']);
		});

		// DB-level guard (works on PostgreSQL; on some MySQL versions CHECK may be ignored)
		try {
			DB::statement("ALTER TABLE time_ranges ADD CONSTRAINT time_ranges_valid CHECK (end_time > start_time)");
		} catch (Throwable) {
			// Ignore if the driver doesn't support adding check constraints this way.
		}
	}

	public function down(): void
	{
		Schema::dropIfExists('time_ranges');
	}
};

