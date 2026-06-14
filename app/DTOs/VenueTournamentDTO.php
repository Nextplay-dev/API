<?php
namespace App\DTOs;
use Illuminate\Foundation\Http\FormRequest;
readonly class VenueTournamentDTO
{
    public function __construct(
        public ?int $venueId = null,
        public ?int $activityId = null,
        public ?string $title = null,
        public ?string $pictureUrl = null,
        public ?string $description = null,
        public ?int $bookingId = null,
        public ?int $spotCount = null,
    ) {}
    public static function fromRequest(FormRequest $request, ?int $venueId = null): self
    {
        return new self(
            venueId: $venueId ?? ($request->has('venue_id') ? (int) $request->validated('venue_id') : null),
            activityId: $request->has('activity_id') ? (int) $request->validated('activity_id') : null,
            title: $request->validated('title'),
            pictureUrl: $request->validated('picture_url'),
            description: $request->validated('description'),
            bookingId: $request->has('booking_id') ? (int) $request->validated('booking_id') : null,
            spotCount: $request->has('spot_count') ? (int) $request->validated('spot_count') : null,
        );
    }
    public function toArray(): array
    {
        $data = [];
        if ($this->venueId !== null) {
            $data['venue_id'] = $this->venueId;
        }
        if ($this->activityId !== null) {
            $data['activity_id'] = $this->activityId;
        }
        if ($this->title !== null) {
            $data['title'] = $this->title;
        }
        if ($this->pictureUrl !== null) {
            $data['picture_url'] = $this->pictureUrl;
        }
        if ($this->description !== null) {
            $data['description'] = $this->description;
        }
        if ($this->bookingId !== null) {
            $data['booking_id'] = $this->bookingId;
        }
        if ($this->spotCount !== null) {
            $data['spot_count'] = $this->spotCount;
        }
        return $data;
    }
}
