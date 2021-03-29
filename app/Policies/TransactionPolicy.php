<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use App\Traits\AdminActions;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization, AdminActions;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Transaction  $transaction
     * @return mixed
     */
    public function view(User $user, Transaction $transaction)
    {
        return $user->id === $transaction->buyer->id ||
            $user->id === $transaction->product->seller->id;
    }
}
