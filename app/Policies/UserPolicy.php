<?php

namespace App\Policies;

use App\Models\User;
use App\Traits\AdminActions;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization, AdminActions;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $authenticatedUser
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function view(User $authenticatedUser, User $user)
    {
        return $authenticatedUser->id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $authenticatedUser
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function update(User $authenticatedUser, User $user)
    {
        return $authenticatedUser->id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $authenticatedUser
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function delete(User $authenticatedUser, User $user)
    {
        return $authenticatedUser->id === $user->id;
    }
}
