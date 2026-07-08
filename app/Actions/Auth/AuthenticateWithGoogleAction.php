<?php

namespace App\Actions\Auth;

use App\DTOs\UserDTO;
use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class AuthenticateWithGoogleAction
{
    public function __construct(
        protected RegisterAction $registerAction
    ) {}

    public function handle(SocialiteUser $googleUser, ?string $originReferrer = null): string
    {
        $user = User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            $userDto = new UserDTO(
                name: $googleUser->getName() ?? $googleUser->getNickname() ?? 'Player',
                email: $googleUser->getEmail(),
                password: null,
                roles: ['customer'],
                originReferrer: $originReferrer
            );

            $user = $this->registerAction->handle($userDto);
            $user->update(['social_provider' => 'google']);
        } else {
            $user->update([
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Player',
                'social_provider' => $user->social_provider ?? 'google',
            ]);
        }

        return $user->createToken('auth')->plainTextToken;
    }
}
