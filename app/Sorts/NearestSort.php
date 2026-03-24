<?php

namespace App\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class NearestSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $latitude = request('latitude');
        $longitude = request('longitude');

        if (!$latitude || !$longitude) {
            return $query;
        }

        return $query->orderByRaw(
            "((latitude - ?) * (latitude - ?) + (longitude - ?) * (longitude - ?)) " . ($descending ? 'DESC' : 'ASC'),
            [$latitude, $latitude, $longitude, $longitude]
        );
    }
}
