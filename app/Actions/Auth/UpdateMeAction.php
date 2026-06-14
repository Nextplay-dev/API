<?php

namespace App\Actions\Auth;

use App\DTOs\UpdateMeDTO;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateMeAction
{
    public function handle(User $user, UpdateMeDTO $dto): User
    {
        return DB::transaction(function () use ($user, $dto) {
            $user->update($dto->toArray());

            return $user;
        });
    }
}
