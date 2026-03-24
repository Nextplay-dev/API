<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class AuthenticateWithGoogleAction
{
    public function handle(SocialiteUser $googleUser): string
    {
        $user = User::updateOrCreate([
            'email' => $googleUser->getEmail(),
        ], [
            'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Player',
            'password' => bcrypt(Str::random(16)),
        ]);

        return $user->createToken('auth')->plainTextToken;
    }
}
