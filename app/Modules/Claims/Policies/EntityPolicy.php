<?php

namespace App\Modules\Claims\Policies;

use App\Models\User;
use App\Modules\Claims\Models\ClaimEntity;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntityPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->role === 'admin' || $user->hasPermission('entities', 'view');
    }

    public function create(User $user)
    {
        return $user->role === 'admin' || $user->hasPermission('entities', 'create');
    }

    public function update(User $user, ClaimEntity $entity)
    {
        return $user->role === 'admin' || $user->hasPermission('entities', 'edit');
    }

    public function delete(User $user, ClaimEntity $entity)
    {
        return $user->role === 'admin' || $user->hasPermission('entities', 'delete');
    }
}
