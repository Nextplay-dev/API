<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venue_opening_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week')->nullable()->comment('0=Monday, 6=Sunday');
            $table->date('exceptional_date')->nullable();
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->string('label')->nullable();
            $table->timestamps();

            $table->index(['venue_id', 'exceptional_date']);
            $table->index(['venue_id', 'day_of_week']);
        });

        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn('opening_hours');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_opening_hours');
    }
};
