<?php

namespace App\Http\Controllers;

use App\Actions\Location\FetchGooglePlaceDetailsAction;
use App\Actions\Location\SearchGoogleCityAutocompleteAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function autocomplete(Request $request, SearchGoogleCityAutocompleteAction $action): JsonResponse
    {
        $query = trim((string) $request->string('query'));

        if ($query === '') {
            return response()->json(['data' => []]);
        }

        return response()->json(['data' => $action->handle($query)]);
    }

    public function show(string $placeId, FetchGooglePlaceDetailsAction $action): JsonResponse
    {
        return response()->json(['data' => $action->handle($placeId)]);
    }
}
