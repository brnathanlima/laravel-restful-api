<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Traits\AdminActions;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization, AdminActions;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Product  $product
     * @return mixed
     */
    public function addCategory(User $user, Product $product)
    {
        return $user->id === $product->seller->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Product  $product
     * @return mixed
     */
    public function removeCategory(User $user, Product $product)
    {
        return $user->id === $product->seller->id;
    }
}
