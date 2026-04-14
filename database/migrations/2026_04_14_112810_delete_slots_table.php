<?php

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
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['slot_id']);
            $table->dropColumn('slot_id');
        });
        Schema::dropIfExists('slots');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_id')->constrained()->cascadeOnDelete();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->integer('capacity');
            $table->timestamps();

            $table->index(['resource_id', 'start_at', 'end_at']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('slot_id')->after('activity_id')->nullable()->constrained()->onDelete('set null');
        });
    }
};
