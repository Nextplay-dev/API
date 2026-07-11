<?php

namespace App\Actions\Analytics;

use App\Models\UserAnalytic;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ListAnalyticsOverviewAction
{
    public function handle(int $page = 1, ?string $actionFilter = null): array
    {
        $stats = [
            'total_events' => UserAnalytic::count(),
            'unique_users' => UserAnalytic::distinct('user_id')->count('user_id'),
        ];

        $topActions = UserAnalytic::select('action', DB::raw('count(*) as count'))
            ->groupBy('action')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $timeline = UserAnalytic::select(
                DB::raw('DATE(created_at) as date'),
                'action',
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->groupBy('date', 'action')
            ->orderBy('date')
            ->get();

        $venueClicks = DB::table('user_analytic_attachments')
            ->join('user_analytics', 'user_analytics.id', '=', 'user_analytic_attachments.user_analytic_id')
            ->join('venues', 'venues.id', '=', 'user_analytic_attachments.attachable_id')
            ->where('user_analytic_attachments.attachable_type', Venue::class)
            ->where('user_analytics.action', 'venue.click_external_booking')
            ->select('venues.id', 'venues.name', DB::raw('count(user_analytics.id) as clicks_count'))
            ->groupBy('venues.id', 'venues.name')
            ->orderByDesc('clicks_count')
            ->get();

        $venueVisits = DB::table('user_analytic_attachments')
            ->join('user_analytics', 'user_analytics.id', '=', 'user_analytic_attachments.user_analytic_id')
            ->join('venues', 'venues.id', '=', 'user_analytic_attachments.attachable_id')
            ->where('user_analytic_attachments.attachable_type', Venue::class)
            ->where('user_analytics.action', 'venue_visit')
            ->select('venues.id', 'venues.name', DB::raw('count(user_analytics.id) as visits_count'))
            ->groupBy('venues.id', 'venues.name')
            ->orderByDesc('visits_count')
            ->get();

        $query = UserAnalytic::with('user')->orderByDesc('created_at');

        if ($actionFilter) {
            $query->where('action', $actionFilter);
        }

        $logs = $query->paginate(20, ['*'], 'page', $page);

        return [
            'stats' => $stats,
            'top_actions' => $topActions,
            'timeline' => $timeline,
            'venue_clicks' => $venueClicks,
            'venue_visits' => $venueVisits,
            'logs' => $logs,
        ];
    }
}
