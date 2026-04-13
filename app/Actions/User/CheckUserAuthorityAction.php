<?php

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CheckUserAuthorityAction
{
    public function handle(User $targetUser): void
    {
        $authUser = Auth::user();

        if ($authUser->id === $targetUser->id) {
            return;
        }

        if ($authUser->getHighestRoleWeight() <= $targetUser->getHighestRoleWeight()) {
            abort(403, 'You do not have enough authority to modify this user.');
        }
    }
}
