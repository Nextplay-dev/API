<?php

use App\Models\User;
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
        Schema::create('user_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('action');
            $table->timestamps();
        });

        Schema::create('user_analytic_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_analytic_id')->constrained()->cascadeOnDelete();
            $table->morphs('attachable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_analytic_attachments');
        Schema::dropIfExists('user_analytics');
    }
};
