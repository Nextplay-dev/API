<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('resource_id')->after('id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('activity_id')->after('resource_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('slot_id')->after('activity_id')->nullable()->constrained()->onDelete('set null');
            $table->dateTime('start_at')->after('slot_id')->nullable();
            $table->dateTime('end_at')->after('start_at')->nullable();
            $table->integer('units')->after('end_at')->default(1);
            $table->string('status')->after('units')->default('pending');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['resource_id']);
            $table->dropForeign(['activity_id']);
            $table->dropForeign(['slot_id']);
            $table->dropColumn(['resource_id', 'activity_id', 'slot_id', 'start_at', 'end_at', 'units', 'status']);
        });
    }
};
