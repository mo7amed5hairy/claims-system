<?php

namespace App\Modules\Claims\Policies;

use App\Models\User;
use App\Modules\Claims\Models\Hospital;
use Illuminate\Auth\Access\HandlesAuthorization;

class HospitalPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->role === 'admin' || $user->hasPermission('hospitals', 'view');
    }

    public function create(User $user)
    {
        return $user->role === 'admin' || $user->hasPermission('hospitals', 'create');
    }

    public function update(User $user, Hospital $hospital)
    {
        return $user->role === 'admin' || $user->hasPermission('hospitals', 'edit');
    }

    public function delete(User $user, Hospital $hospital)
    {
        return $user->role === 'admin' || $user->hasPermission('hospitals', 'delete');
    }
}
