<?php

namespace App\Console\Commands;

use App\Actions\Booking\GenerateSlotsAction;
use App\Models\Activity;
use App\Models\Resource;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateSlotsCommand extends Command
{
    protected $signature = 'slots:generate {--date= : Specific date to generate slots for} {--days=7 : Number of days to generate slots for}';
    protected $description = 'Generate availability slots for resources';

    public function handle(GenerateSlotsAction $action): int
    {
        $startDate = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();
        $days = (int) $this->option('days');

        $resources = Resource::all();
        $baseActivity = Activity::first(); // Or some logic to determine the base activity

        if (!$baseActivity) {
            $this->error('No activity found to base slots on. Please create at least one activity.');
            return 1;
        }

        $this->info("Generating slots starting from {$startDate->toDateString()} for {$days} days...");

        foreach ($resources as $resource) {
            for ($i = 0; $i < $days; $i++) {
                $date = $startDate->copy()->addDays($i)->toDateString();
                $action->handle($resource, $baseActivity, $date);
            }
        }

        $this->info('Slot generation completed successfully!');

        return 0;
    }
}
