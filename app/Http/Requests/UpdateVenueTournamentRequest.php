<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UpdateVenueTournamentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'activity_id' => ['sometimes', 'integer', 'exists:activities,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'picture_url' => ['nullable', 'string', 'max:2048'],
            'description' => ['nullable', 'string'],
        ];
    }
}
