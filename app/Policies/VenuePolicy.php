<?php

namespace App\Policies;

use App\Models\Venue;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class VenuePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Venue $venue): bool
    {
        return true;
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
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasPermissionTo('venue.update')) {
            return $venue->managers()->where('users.id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Venue $venue): bool
    {
        return $user->hasRole('admin');
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
