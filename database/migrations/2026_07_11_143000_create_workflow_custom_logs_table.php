<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_custom_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stored_workflow_id')->index();
            $table->text('message');
            $table->timestamp('created_at', 6)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_custom_logs');
    }
};
