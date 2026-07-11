<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->string('google_place_id')->nullable()->unique()->after('osm_id');
            $table->string('phone')->nullable()->after('website');
            $table->json('opening_hours')->nullable()->after('description');
            $table->string('google_photo_reference')->nullable()->after('media');
        });
    }

    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn(['google_place_id', 'phone', 'opening_hours', 'google_photo_reference']);
        });
    }
};
