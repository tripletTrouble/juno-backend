<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrganizationPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organization $organization): bool
    {
        if ($organization->members()->where('users.id', $user->id)->first()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->owned_organizations()->count() < 5;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function manage(User $user, Organization $organization): bool
    {
        return $organization->user_id == $user->id;
    }

    /**
     * Determine whether the user can create transactions
     */
    public function createTransactions(User $user, Organization $organization): bool
    {
        if ($organization->members()->where('users.id', $user->id)->first()) {
            return true;
        }

        return false;
    }
}
