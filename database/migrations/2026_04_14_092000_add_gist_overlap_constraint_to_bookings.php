<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, we need to ensure the btree_gist extension is enabled for multi-column exclusion
        DB::statement('CREATE EXTENSION IF NOT EXISTS btree_gist');

        DB::statement("
            ALTER TABLE bookings
            ADD CONSTRAINT bookings_overlap_exclusion
            EXCLUDE USING gist (
                resource_id WITH =,
                tsrange(start_at, end_at) WITH &&
            )
            WHERE (status = 'confirmed')
        ");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE bookings DROP CONSTRAINT IF EXISTS bookings_overlap_exclusion');
    }
};
