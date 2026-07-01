<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreVenueTournamentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'activity_id' => ['required', 'integer', 'exists:activities,id'],
            'title' => ['required', 'string', 'max:255'],
            'picture_url' => ['nullable', 'string', 'max:2048'],
            'description' => ['nullable', 'string'],
            'booking_id' => ['nullable', 'integer', 'exists:bookings,id'],
            'spot_count' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
