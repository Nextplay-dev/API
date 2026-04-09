<?php

namespace App\Actions\User;

use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StoreUserAction
{
    public function handle(UserDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $user = User::create($dto->toArray());

            if (!empty($dto->roles)) {
                $user->assignRole(...$dto->roles);
            }

            return $user;
        });
    }
}
