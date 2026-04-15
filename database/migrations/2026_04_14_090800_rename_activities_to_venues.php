<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('activities', 'venues');
        Schema::rename('activity_categories', 'categories');
        Schema::rename('activity_tournament', 'tournament_venue');
        Schema::rename('activity_user', 'user_venue');

        Schema::table('venues', function (Blueprint $table) {
            $table->renameColumn('activity_category_id', 'category_id');
        });

        Schema::table('tournament_venue', function (Blueprint $table) {
            $table->renameColumn('activity_id', 'venue_id');
        });

        Schema::table('user_venue', function (Blueprint $table) {
            $table->renameColumn('activity_id', 'venue_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_venue', function (Blueprint $table) {
            $table->renameColumn('venue_id', 'activity_id');
        });

        Schema::table('tournament_venue', function (Blueprint $table) {
            $table->renameColumn('venue_id', 'activity_id');
        });

        Schema::table('venues', function (Blueprint $table) {
            $table->renameColumn('category_id', 'activity_category_id');
        });

        Schema::rename('user_venue', 'activity_user');
        Schema::rename('tournament_venue', 'activity_tournament');
        Schema::rename('categories', 'activity_categories');
        Schema::rename('venues', 'activities');
    }
};
