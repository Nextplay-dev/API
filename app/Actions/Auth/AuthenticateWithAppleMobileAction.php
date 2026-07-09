<?php

namespace App\Actions\Auth;

use App\DTOs\AppleMobileAuthDTO;
use App\Models\User;
use App\DTOs\UserDTO;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;

class AuthenticateWithAppleMobileAction
{
    public function __construct(
        protected RegisterAction $registerAction
    ) {}

    public function handle(AppleMobileAuthDTO $dto): string
    {
        $response = Http::get('https://appleid.apple.com/auth/keys');
        $keys = $response->json();

        $decoded = JWT::decode($dto->identityToken, JWK::parseKeySet($keys));

        if ($decoded->iss !== 'https://appleid.apple.com') {
            throw new \Exception('Invalid issuer');
        }

        $allowedAudience = config('services.apple.mobile_client_id');
        if ($decoded->aud !== $allowedAudience) {
            throw new \Exception('Invalid audience');
        }

        $email = $decoded->email ?? null;

        if (! $email) {
            throw new \Exception('Email not present in identity token');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $userDto = new UserDTO(
                name: $dto->name ?? 'Player',
                email: $email,
                password: null,
                roles: ['customer']
            );

            $user = $this->registerAction->handle($userDto);
            $user->update([
                'social_provider' => 'apple',
            ]);
        } else {
            $user->update([
                'social_provider' => $user->social_provider ?? 'apple',
            ]);
        }

        return $user->createToken('auth')->plainTextToken;
    }
}
