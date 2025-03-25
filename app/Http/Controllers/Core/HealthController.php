<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        // Check if the application is healthy

        // Check database connection
        $this->errorResponse('database', function () {
            DB::statement('SELECT 1');
        }, 'Database connection failed');

        return response()->json([
            'status' => 'ok',
        ]);
    }

    private function errorResponse(string $resource, callable $test, ?string $message): void
    {
        try {
            $test();
        } catch (\Exception $e) {
            abort(503, $message ?? 'Service unavailable');
        }
    }
}
