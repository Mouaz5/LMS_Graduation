<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->id === $payment->parent_user_id;
    }
}
