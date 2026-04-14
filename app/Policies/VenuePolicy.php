<?php

namespace App\Policies;

use App\Models\Venue;
use App\Models\User;

class VenuePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('venue.view') || $user->hasPermissionTo('my-venue.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Venue $venue): bool
    {
        return $user->hasPermissionTo('venue.view') || $user->hasPermissionTo('my-venue.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('venue.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Venue $venue): bool
    {
        if ($user->hasPermissionTo('venue.update')) {
            return true;
        }

        if ($user->hasPermissionTo('my-venue.update')) {
            return $venue->managers()->where('users.id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Venue $venue): bool
    {
        return $user->hasPermissionTo('venue.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Venue $venue): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Venue $venue): bool
    {
        return false;
    }
}
