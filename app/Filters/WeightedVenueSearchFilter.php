<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class WeightedVenueSearchFilter implements Filter
{
    public function __invoke(Builder $query, mixed $value, string $property): void
    {
        $searchTerm = mb_strtolower(trim((string) $value));

        if ($searchTerm === '') {
            return;
        }

        $tokens = preg_split('/[^\pL\pN]+/u', $searchTerm) ?: [];
        $tokens = array_values(array_filter($tokens, fn (string $token) => $token !== ''));

        if ($tokens === []) {
            return;
        }

        $query->leftJoin('categories', 'venues.category_id', '=', 'categories.id');

        $query->where(function (Builder $query) use ($tokens) {
            foreach ($tokens as $token) {
                $query->where(function (Builder $query) use ($token) {
                    $query->whereRaw('LOWER(venues.name) LIKE ?', ['%' . $token . '%'])
                        ->orWhereRaw('LOWER(venues.address) LIKE ?', ['%' . $token . '%'])
                        ->orWhereRaw('LOWER(categories.name) LIKE ?', ['%' . $token . '%']);
                });
            }
        });

        $bindings = [];
        $scoreParts = [];

        $fullSearchTerm = '%' . $searchTerm . '%';
        $scoreParts[] = 'CASE WHEN LOWER(venues.name) LIKE ? THEN 120 ELSE 0 END';
        $bindings[] = $fullSearchTerm;
        $scoreParts[] = 'CASE WHEN LOWER(categories.name) LIKE ? THEN 100 ELSE 0 END';
        $bindings[] = $fullSearchTerm;
        $scoreParts[] = 'CASE WHEN LOWER(venues.address) LIKE ? THEN 50 ELSE 0 END';
        $bindings[] = $fullSearchTerm;

        foreach ($tokens as $token) {
            $tokenLike = '%' . $token . '%';

            $scoreParts[] = 'CASE WHEN LOWER(venues.name) LIKE ? THEN 35 ELSE 0 END';
            $bindings[] = $tokenLike;

            $scoreParts[] = 'CASE WHEN LOWER(venues.name) LIKE ? THEN 15 ELSE 0 END';
            $bindings[] = $token . '%';

            $scoreParts[] = 'CASE WHEN LOWER(categories.name) LIKE ? THEN 25 ELSE 0 END';
            $bindings[] = $tokenLike;

            $scoreParts[] = 'CASE WHEN LOWER(venues.address) LIKE ? THEN 10 ELSE 0 END';
            $bindings[] = $tokenLike;
        }

        $query
            ->selectRaw('venues.* , (' . implode(' + ', $scoreParts) . ') as search_score', $bindings)
            ->orderByDesc('search_score')
            ->orderBy('venues.name');
    }
}