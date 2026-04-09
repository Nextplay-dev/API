<?php

use App\Models\Activity;
use App\Models\ActivityCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->foreignIdFor(ActivityCategory::class)->nullable()->after('media')->constrained()->nullOnDelete();
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('category')->after('media');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(ActivityCategory::class);
        });
    }
};
