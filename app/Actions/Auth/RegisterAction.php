<?php

namespace App\Actions\Auth;

use App\Actions\Analytics\LogUserAnalyticAction;
use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterAction
{
    public function __construct(
        private readonly LogUserAnalyticAction $logAnalytic
    ) {}

    public function handle(UserDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $user = User::create([
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => $dto->password ?? bcrypt(Str::random(16)),
            ]);

            $user->assignRole('customer');

            $this->logAnalytic->handle(
                user: $user,
                action: 'user_registered',
                metadata: [
                    'origin_referrer' => $dto->originReferrer,
                ]
            );

            return $user;
        });
    }
}
