<?php

namespace App\Actions\Booking;

use App\DTOs\CreateBookingDTO;
use App\Models\Activity;
use App\Models\Booking;
use App\Models\Resource;
use App\Notifications\BookingConfirmed;
use App\Services\BookingValidatorService;
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

        abort_unless($resource->venue_id === $activity->venue_id, 422, 'Mismatched resources and activities');

        if (! $this->bookingValidatorService->canBook($resource, $activity, $dto->start_at, $dto->end_at, $dto->units)) {
            throw ValidationException::withMessages([
                'booking' => 'The selected slot is no longer available or overlaps with an existing booking.',
            ]);
        }

        $booking = DB::transaction(function () use ($dto) {
            return Booking::create([
                'user_id' => auth()->id(),
                'resource_id' => $dto->resource_id,
                'activity_id' => $dto->activity_id,
                'start_at' => $dto->start_at,
                'end_at' => $dto->end_at,
                'units' => $dto->units,
                'status' => 'confirmed',
                'payment' => true,
            ]);
        });

        auth()->user()->notify(new BookingConfirmed($booking));

        return $booking;
    }
}
