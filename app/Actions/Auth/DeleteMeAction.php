<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DeleteMeAction
{
    public function handle(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->forceFill([
                'name' => 'Deleted User',
                'email' => 'deleted_' . $user->id . '@nextplay.app',
                'password' => Hash::make(Str::random(40)),
                'bio' => null,
                'picture_profile_url' => null,
                'social_provider' => null,
                'enable_core_notification' => false,
                'enable_commercial_notification' => false,
                'email_verified_at' => null,
            ])->save();

            $user->roles()->detach();
            $user->permissions()->detach();

            $user->tokens()->delete();
        });
    }
}
