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

        if (! $latitude || ! $longitude) {
            return $query;
        }

        // radius in meters (50 km default)
        $radiusMeters = 50000;

        // Haversine distance expression (meters)
        $distanceExpr = '6371000 * 2 * asin(sqrt(power(sin(radians(venues.latitude - ?)/2),2) + cos(radians(?)) * cos(radians(venues.latitude)) * power(sin(radians(venues.longitude - ?)/2),2)))';

        // Use the haversine expression inline in ORDER BY so it does not rely on a
        // selected alias (which can be removed by other query parts).
        $expr = "($distanceExpr)";

        // Parameters for each ORDER BY call
        $paramsFirst = [$latitude, $latitude, $longitude, $radiusMeters];
        $paramsSecond = [$latitude, $latitude, $longitude, $radiusMeters, $latitude, $latitude, $longitude];

        return $query
            ->orderByRaw('CASE WHEN '.$expr.' <= ? THEN 0 ELSE 1 END ASC', $paramsFirst)
            ->orderByRaw('CASE WHEN '.$expr.' <= ? THEN 0 ELSE '.$expr.' END '.($descending ? 'DESC' : 'ASC'), $paramsSecond);
    }
}
