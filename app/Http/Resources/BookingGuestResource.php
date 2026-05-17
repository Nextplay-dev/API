<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingGuestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'email' => $this->email,
            'token' => $this->token,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'user' => PublicUserResource::make($this->whenLoaded('user')),
        ];
    }
}
