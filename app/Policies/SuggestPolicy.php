<?php

namespace App\Policies;

use App\User;
use App\Suggest;
use Illuminate\Auth\Access\HandlesAuthorization;

class SuggestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the suggest.
     *
     * @param  \App\User  $user
     * @param  \App\Suggest  $suggest
     * @return mixed
     */
    public function view(User $user, Suggest $suggest)
    {
        return true;
    }

    /**
     * Determine whether the user can create suggests.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the suggest.
     *
     * @param  \App\User  $user
     * @param  \App\Suggest  $suggest
     * @return mixed
     */
    public function update(User $user, Suggest $suggest)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the suggest.
     *
     * @param  \App\User  $user
     * @param  \App\Suggest  $suggest
     * @return mixed
     */
    public function delete(User $user, Suggest $suggest)
    {
        return $user->hasRole('admin');
    }
}
