<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActivityPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user, Venue $venue): bool
    {
        return $user->hasPermissionTo('activity.view') || 
            ($user->hasPermissionTo('my-venue.view') && $venue->managers()->whereUserId($user->id)->exists());
    }

    public function view(User $user, Activity $activity): bool
    {
        return $user->hasPermissionTo('activity.view') || 
            ($user->hasPermissionTo('my-venue.view') && $activity->venue->managers()->whereUserId($user->id)->exists());
    }

    public function create(User $user, Venue $venue): bool
    {
        return $user->hasPermissionTo('activity.create') || 
            ($user->hasPermissionTo('my-venue.update') && $venue->managers()->whereUserId($user->id)->exists());
    }

    public function update(User $user, Activity $activity): bool
    {
        return $user->hasPermissionTo('activity.update') || 
            ($user->hasPermissionTo('my-venue.update') && $activity->venue->managers()->whereUserId($user->id)->exists());
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $user->hasPermissionTo('activity.delete') || 
            ($user->hasPermissionTo('my-venue.delete') && $activity->venue->managers()->whereUserId($user->id)->exists());
    }
}
