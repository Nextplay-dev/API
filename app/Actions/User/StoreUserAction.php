<?php

namespace App\Actions\User;

use App\Actions\Role\FilterAllowedRolesAction;
use App\DTOs\UserDTO;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StoreUserAction
{
    public function __construct(
        protected FilterAllowedRolesAction $filterAllowedRolesAction
    ) {}

    public function handle(UserDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $user = User::create($dto->toArray());

            if ($dto->roles !== null) {
                $rolesToAssign = $this->filterAllowedRolesAction->handle(
                    auth()->user(),
                    $dto->roles
                );

                if (! empty($rolesToAssign)) {
                    $user->assignRole(...$rolesToAssign);
                }
            }

            return $user;
        });
    }
}
