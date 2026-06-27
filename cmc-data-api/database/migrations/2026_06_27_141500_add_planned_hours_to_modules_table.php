<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            if (! Schema::hasColumn('modules', 'mh_presentiel')) {
                $table->decimal('mh_presentiel', 8, 2)->default(0)->after('regional');
            }

            if (! Schema::hasColumn('modules', 'mh_syn')) {
                $table->decimal('mh_syn', 8, 2)->default(0)->after('mh_presentiel');
            }

            if (! Schema::hasColumn('modules', 'mh_asyn')) {
                $table->decimal('mh_asyn', 8, 2)->default(0)->after('mh_syn');
            }

            if (! Schema::hasColumn('modules', 'mh_totale')) {
                $table->decimal('mh_totale', 8, 2)->default(0)->after('mh_asyn');
            }
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            foreach (['mh_totale', 'mh_asyn', 'mh_syn', 'mh_presentiel'] as $column) {
                if (Schema::hasColumn('modules', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
