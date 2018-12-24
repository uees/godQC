<?php

namespace App\Policies;

use App\User;
use App\TestWay;
use Illuminate\Auth\Access\HandlesAuthorization;

class TestWayPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the testWay.
     *
     * @param  \App\User  $user
     * @param  \App\TestWay  $testWay
     * @return mixed
     */
    public function view(User $user, TestWay $testWay)
    {
        return true;
    }

    /**
     * Determine whether the user can create testWays.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the testWay.
     *
     * @param  \App\User  $user
     * @param  \App\TestWay  $testWay
     * @return mixed
     */
    public function update(User $user, TestWay $testWay)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the testWay.
     *
     * @param  \App\User  $user
     * @param  \App\TestWay  $testWay
     * @return mixed
     */
    public function delete(User $user, TestWay $testWay)
    {
        return $user->hasRole('admin');
    }
}
