<?php

namespace App\Actions\Auth;

use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegisterAction
{
    public function handle(UserDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $user = User::create([
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => $dto->password ?? bcrypt(Str::random(16)),
            ]);

            $user->assignRole('customer');

            return $user;
        });
    }
}
