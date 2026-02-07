<?php

namespace App\Modules\Claims\Policies;

use App\Models\User;
use App\Modules\Claims\Models\Claim;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClaimPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any claims.
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('claims', 'view');
    }

    /**
     * Determine if the user can view the claim.
     */
    public function view(User $user, Claim $claim)
    {
        return $user->hasPermission('claims', 'view');
    }

    /**
     * Determine if the user can create claims.
     */
    public function create(User $user)
    {
        return $user->hasPermission('claims', 'add');
    }

    /**
     * Determine if the user can update the claim.
     */
    public function update(User $user, Claim $claim)
    {
        return $user->hasPermission('claims', 'edit');
    }

    /**
     * Determine if the user can delete the claim.
     */
    public function delete(User $user, Claim $claim)
    {
        return $user->hasPermission('claims', 'delete');
    }
}
