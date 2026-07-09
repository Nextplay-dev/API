<?php

namespace App\Actions\Auth;

use App\Models\User;

class TryEmailAction
{
    public function handle(string $email): array
    {
        $user = User::where('email', $email)->firstOrFail();

        return [
            'name' => $user->name,
            'picture_profile_url' => $user->picture_profile_url,
            'method' => $user->social_provider ? 'social': 'password',
        ];
    }
}
