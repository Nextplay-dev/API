<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['resource_id', 'start_at', 'end_at'], 'bookings_resource_time_idx');
            $table->index(['status'], 'bookings_status_idx');
        });

        Schema::table('availabilities', function (Blueprint $table) {
            $table->index(['resource_id', 'day_of_week'], 'availabilities_resource_day_idx');
        });

        Schema::table('exceptions', function (Blueprint $table) {
            $table->index(['resource_id', 'start_at', 'end_at'], 'exceptions_resource_time_idx');
        });

        DB::statement("
            CREATE INDEX bookings_confirmed_overlap_idx
            ON bookings (resource_id, start_at, end_at)
            WHERE status = 'confirmed'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('bookings_resource_time_idx');
            $table->dropIndex('bookings_status_idx');
        });

        Schema::table('availabilities', function (Blueprint $table) {
            $table->dropIndex('availabilities_resource_day_idx');
        });

        Schema::table('exceptions', function (Blueprint $table) {
            $table->dropIndex('exceptions_resource_time_idx');
        });

        DB::statement("DROP INDEX IF EXISTS bookings_confirmed_overlap_idx");
    }
};
