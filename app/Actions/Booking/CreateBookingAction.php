<?php

namespace App\Actions\Booking;

use App\DTOs\CreateBookingDTO;
use App\Models\Activity;
use App\Models\Booking;
use App\Models\Resource;
use App\Services\BookingValidatorService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateBookingAction
{
    public function __construct(
        protected BookingValidatorService $bookingValidatorService
    ) {}

    public function handle(CreateBookingDTO $dto): Booking
    {
        $resource = Resource::findOrFail($dto->resource_id);
        $activity = Activity::findOrFail($dto->activity_id);
        $start_at = Carbon::parse($dto->start_at);
        $end_at = Carbon::parse($dto->end_at);

        if (!$this->bookingValidatorService->canBook($resource, $activity, $start_at, $end_at, $dto->units)) {
            throw ValidationException::withMessages([
                'booking' => 'The selected slot is no longer available or overlaps with an existing booking.',
            ]);
        }

        return DB::transaction(function () use ($dto, $start_at, $end_at) {
            return Booking::create([
                'user_id' => auth()->id(),
                'resource_id' => $dto->resource_id,
                'activity_id' => $dto->activity_id,
                'start_at' => $start_at,
                'end_at' => $end_at,
                'units' => $dto->units,
                'status' => 'confirmed',
                'payment' => true, // Defaulting for now
            ]);
        });
    }
}
