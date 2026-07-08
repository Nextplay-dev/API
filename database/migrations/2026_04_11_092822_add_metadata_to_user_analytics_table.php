<?php

/**
 * Migration to add metadata column to user_analytics table.
 */

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
        Schema::table('user_analytics', function (Blueprint $table) {
            $table->json('metadata')->nullable()->after('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_analytics', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
    }
};
