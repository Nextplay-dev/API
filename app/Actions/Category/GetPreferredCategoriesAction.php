<?php

namespace App\Actions\Category;

use App\Models\Booking;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Collection;

class GetPreferredCategoriesAction
{
    public function handle(User $user): Collection
    {
        $bookedCategoryCounts = Booking::query()
            ->join('resources', 'bookings.resource_id', '=', 'resources.id')
            ->join('venues', 'resources.venue_id', '=', 'venues.id')
            ->join('categories', 'venues.category_id', '=', 'categories.id')
            ->where('bookings.user_id', $user->id)
            ->selectRaw('categories.id, COUNT(*) as bookings_count')
            ->groupBy('categories.id')
            ->orderByDesc('bookings_count')
            ->pluck('bookings_count', 'categories.id');

        $preferredCategories = collect();

        if ($bookedCategoryCounts->isNotEmpty()) {
            $preferredCategories = Category::query()
                ->whereIn('id', $bookedCategoryCounts->keys())
                ->get()
                ->sortByDesc(fn (Category $category) => (int) $bookedCategoryCounts->get($category->id, 0))
                ->values()
                ->map(function (Category $category) use ($bookedCategoryCounts) {
                    $category->setAttribute('bookings_count', (int) $bookedCategoryCounts->get($category->id, 0));

                    return $category;
                });
        }

        $remainingCount = max(0, 5 - $preferredCategories->count());

        if ($remainingCount === 0) {
            return $preferredCategories->take(5)->values();
        }

        $remainingCategories = Category::query()
            ->when(
                $preferredCategories->isNotEmpty(),
                fn ($query) => $query->whereNotIn('id', $preferredCategories->pluck('id')),
            )
            ->inRandomOrder()
            ->take($remainingCount)
            ->get()
            ->map(function (Category $category) {
                $category->setAttribute('bookings_count', 0);

                return $category;
            });

        return $preferredCategories
            ->concat($remainingCategories)
            ->take(5)
            ->values();
    }
}
