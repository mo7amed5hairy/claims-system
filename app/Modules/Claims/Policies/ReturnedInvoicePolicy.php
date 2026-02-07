<?php

namespace App\Modules\Claims\Policies;

use App\Models\User;
use App\Modules\Claims\Models\ReturnedInvoice;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReturnedInvoicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('returns', 'view');
    }

    public function view(User $user, ReturnedInvoice $return)
    {
        return $user->hasPermission('returns', 'view');
    }

    public function create(User $user)
    {
        return $user->hasPermission('returns', 'add');
    }

    public function update(User $user, ReturnedInvoice $return)
    {
        return $user->hasPermission('returns', 'edit');
    }

    public function delete(User $user, ReturnedInvoice $return)
    {
        return $user->hasPermission('returns', 'delete');
    }
}
