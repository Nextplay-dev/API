<?php

namespace App\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class TournamentNearestSort implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $latitude = request('latitude');
        $longitude = request('longitude');

        if (!$latitude || !$longitude) {
            return $query;
        }

        $radiusMeters = 50000;
        $distanceExpr = '6371000 * 2 * asin(sqrt(power(sin(radians(venues.latitude - ?)/2),2) + cos(radians(?)) * cos(radians(venues.latitude)) * power(sin(radians(venues.longitude - ?)/2),2)))';
        $expr = "($distanceExpr)";
        $paramsFirst = [$latitude, $latitude, $longitude, $radiusMeters];
        $paramsSecond = [$latitude, $latitude, $longitude, $radiusMeters, $latitude, $latitude, $longitude];

        return $query
            ->join('venues', 'venue_tournaments.venue_id', '=', 'venues.id')
            ->select('venue_tournaments.*')
            ->orderByRaw('CASE WHEN ' . $expr . ' <= ? THEN 0 ELSE 1 END ASC', $paramsFirst)
            ->orderByRaw('CASE WHEN ' . $expr . ' <= ? THEN 0 ELSE ' . $expr . ' END ' . ($descending ? 'DESC' : 'ASC'), $paramsSecond);
    }
}
