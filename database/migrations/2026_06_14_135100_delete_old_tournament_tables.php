<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('tournament_venue');
        Schema::dropIfExists('booking_tournament');
        Schema::dropIfExists('tournaments');
    }

    public function down(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('game')->nullable();
            $table->string('media');
            $table->string('level');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->timestamps();
        });

        Schema::create('booking_tournament', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('tournament_venue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->onDelete('cascade');
            $table->foreignId('tournament_id')->constrained()->onDelete('cascade');
            $table->boolean('host')->default(false);
            $table->timestamps();
        });
    }
};
