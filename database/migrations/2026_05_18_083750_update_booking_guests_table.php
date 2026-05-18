<?php

use App\Models\Notification;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_guests', function (Blueprint $table) {
            $table->foreignIdFor(Notification::class)->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('booking_guests', function (Blueprint $table) {
            $table->dropForeignIdFor(Notification::class);
        });
    }
};
