<?php

namespace App\Policies;

use App\User;
use App\ProductDispose;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductDisposePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the productDispose.
     *
     * @param  \App\User $user
     * @param  \App\ProductDispose $productDispose
     * @return mixed
     */
    public function view(User $user, ProductDispose $productDispose)
    {
        return true;
    }

    /**
     * Determine whether the user can create productDisposes.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the productDispose.
     *
     * @param  \App\User $user
     * @param  \App\ProductDispose $productDispose
     * @return mixed
     */
    public function update(User $user, ProductDispose $productDispose)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the productDispose.
     *
     * @param  \App\User $user
     * @param  \App\ProductDispose $productDispose
     * @return mixed
     */
    public function delete(User $user, ProductDispose $productDispose)
    {
        return $user->hasRole('admin');
    }
}
