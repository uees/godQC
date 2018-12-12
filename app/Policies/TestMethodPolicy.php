<?php

namespace App\Policies;

use App\User;
use App\TestMethod;
use Illuminate\Auth\Access\HandlesAuthorization;

class TestMethodPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the testMethod.
     *
     * @param  \App\User  $user
     * @param  \App\TestMethod  $testMethod
     * @return mixed
     */
    public function view(User $user, TestMethod $testMethod)
    {
        //
    }

    /**
     * Determine whether the user can create testMethods.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the testMethod.
     *
     * @param  \App\User  $user
     * @param  \App\TestMethod  $testMethod
     * @return mixed
     */
    public function update(User $user, TestMethod $testMethod)
    {
        //
    }

    /**
     * Determine whether the user can delete the testMethod.
     *
     * @param  \App\User  $user
     * @param  \App\TestMethod  $testMethod
     * @return mixed
     */
    public function delete(User $user, TestMethod $testMethod)
    {
        return $user->hasRole('admin');
    }
}
