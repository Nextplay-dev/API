<?php

namespace App\Actions\User;

use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateUserAction
{
    public function handle(User $user, UserDTO $dto): User
    {
        return DB::transaction(function () use ($user, $dto) {
            $user->update($dto->toArray());

            if (!empty($dto->roles)) {
                $user->roles()->sync([]);
                $user->assignRole(...$dto->roles);
            }

            return $user;
        });
    }
}
