<?php

namespace App\Actions\Auth;

use App\DTOs\UserDTO;
use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class AuthenticateWithAppleAction
{
    public function __construct(
        protected RegisterAction $registerAction
    ) {}

    public function handle(SocialiteUser $appleUser, ?string $originReferrer = null): string
    {
        $user = User::where('email', $appleUser->getEmail())->first();

        if (! $user) {
            $userDto = new UserDTO(
                name: $appleUser->getName() ?? $appleUser->getNickname() ?? 'Player',
                email: $appleUser->getEmail(),
                password: null,
                roles: ['customer'],
                originReferrer: $originReferrer
            );

            $user = $this->registerAction->handle($userDto);
            $user->update(['social_provider' => 'apple']);
        } else {
            $user->update([
                'name' => $appleUser->getName() ?? $appleUser->getNickname() ?? $user->name ?? 'Player',
                'social_provider' => $user->social_provider ?? 'apple',
            ]);
        }

        return $user->createToken('auth')->plainTextToken;
    }
}
