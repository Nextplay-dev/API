<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class VenueTournamentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'venue_id' => $this->venue_id,
            'activity_id' => $this->activity_id,
            'booking_id' => $this->booking_id,
            'spot_count' => $this->spot_count,
            'title' => $this->title,
            'picture_url' => $this->picture_url,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'bookings_count' => $this->booking && $this->booking->relationLoaded('guests')
                ? $this->booking->guests->count()
                : ($this->booking ? $this->booking->guests()->count() : 0),
            'activity' => ActivityResource::make($this->whenLoaded('activity')),
            'booking' => BookingResource::make($this->whenLoaded('booking')),
        ];
    }
}
