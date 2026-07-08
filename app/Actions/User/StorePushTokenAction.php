<?php

namespace App\Actions\User;

use App\DTOs\StorePushTokenDTO;
use App\Models\User;
use App\Models\UserNotificationToken;

class StorePushTokenAction
{
    public function handle(User $user, StorePushTokenDTO $dto): UserNotificationToken
    {
        $token = null;

        if ($dto->pushToken) {
            $token = UserNotificationToken::where('push_token', $dto->pushToken)->first();
        }

        if (! $token && $dto->deviceToken) {
            $token = UserNotificationToken::where('device_token', $dto->deviceToken)->first();
        }

        if ($token) {
            $token->update([
                'user_id' => $user->id,
                'push_token' => $dto->pushToken,
                'device_token' => $dto->deviceToken,
                'device_type' => $dto->deviceType,
            ]);

            return $token;
        }

        return UserNotificationToken::create([
            'user_id' => $user->id,
            'push_token' => $dto->pushToken,
            'device_token' => $dto->deviceToken,
            'device_type' => $dto->deviceType,
        ]);
    }
}
