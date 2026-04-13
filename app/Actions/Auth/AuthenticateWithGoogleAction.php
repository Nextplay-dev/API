<?php

namespace App\Actions\Auth;

use App\Actions\Auth\RegisterAction;
use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class AuthenticateWithGoogleAction
{
    public function __construct(
        protected RegisterAction $registerAction
    ) {}

    public function handle(SocialiteUser $googleUser, ?string $originReferrer = null): string
    {
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            $userDto = new UserDTO(
                name: $googleUser->getName() ?? $googleUser->getNickname() ?? 'Player',
                email: $googleUser->getEmail(),
                password: null,
                roles: ['customer'],
                originReferrer: $originReferrer
            );

            $user = $this->registerAction->handle($userDto);
        } else {
            $user->update([
                'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Player',
            ]);
        }

        return $user->createToken('auth')->plainTextToken;
    }
}
