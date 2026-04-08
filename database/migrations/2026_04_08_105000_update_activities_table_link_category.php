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

        // Migrate data from string column to foreign key
        $activities = Activity::all();

        foreach ($activities as $activity) {
            $categoryName = str_replace('Category.', '', $activity->category);
            
            $category = ActivityCategory::where('name', $categoryName)->first();

            if ($category) {
                $activity->update(['activity_category_id' => $category->id]);
            }
        }

        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('category')->after('media');
        });

        $activities = Activity::all();

        foreach ($activities as $activity) {
            if ($activity->activity_category_id) {
                $category = ActivityCategory::find($activity->activity_category_id);
                if ($category) {
                    $activity->update(['category' => 'Category.' . $category->name]);
                }
            }
        }

        Schema::table('activities', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(ActivityCategory::class);
        });
    }
};
