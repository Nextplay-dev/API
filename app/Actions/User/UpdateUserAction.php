<?php

namespace App\Actions\User;

use App\Actions\Role\FilterAllowedRolesAction;
use App\DTOs\UserDTO;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateUserAction
{
    public function __construct(
        protected FilterAllowedRolesAction $filterAllowedRolesAction,
        protected CheckUserAuthorityAction $checkUserAuthorityAction
    ) {}

    public function handle(User $user, UserDTO $dto): User
    {
        $this->checkUserAuthorityAction->handle($user);

        return DB::transaction(function () use ($user, $dto) {
            $user->update($dto->toArray());

            if ($dto->roles !== null && Auth::user()->id !== $user->id) {
                $authUser = Auth::user();
                
                $protectedRoles = $user->roles()
                    ->with('permissions')
                    ->get()
                    ->filter(fn (Role $role) => !$this->filterAllowedRolesAction->isRoleGiveable($authUser, $role))
                    ->pluck('id')
                    ->toArray();

                $rolesToAssignNames = $this->filterAllowedRolesAction->handle(
                    $authUser,
                    $dto->roles
                );
                
                $allowedRolesFromRequestIds = Role::whereIn('name', $rolesToAssignNames)->pluck('id')->toArray();

                $user->roles()->sync(array_unique(array_merge($protectedRoles, $allowedRolesFromRequestIds)));
            }

            return $user;
        });
    }
}
