<?php

namespace App\Policies;

use App\User;
use App\ProductBatch;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductBatchPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the productBatch.
     *
     * @param  \App\User  $user
     * @param  \App\ProductBatch  $productBatch
     * @return mixed
     */
    public function view(User $user, ProductBatch $productBatch)
    {
        return true;
    }

    /**
     * Determine whether the user can create productBatches.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the productBatch.
     *
     * @param  \App\User  $user
     * @param  \App\ProductBatch  $productBatch
     * @return mixed
     */
    public function update(User $user, ProductBatch $productBatch)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the productBatch.
     *
     * @param  \App\User  $user
     * @param  \App\ProductBatch  $productBatch
     * @return mixed
     */
    public function delete(User $user, ProductBatch $productBatch)
    {
        return $user->hasRole('admin');
    }
}
