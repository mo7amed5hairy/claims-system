<?php

namespace App\Modules\Claims\Policies;

use App\Models\User;
use App\Modules\Claims\Models\PaymentOrder;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentOrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermission('payments', 'view');
    }

    public function view(User $user, PaymentOrder $payment)
    {
        return $user->hasPermission('payments', 'view');
    }

    public function create(User $user)
    {
        return $user->hasPermission('payments', 'add');
    }

    public function update(User $user, PaymentOrder $payment)
    {
        return $user->hasPermission('payments', 'edit');
    }

    public function delete(User $user, PaymentOrder $payment)
    {
        return $user->hasPermission('payments', 'delete');
    }
}
