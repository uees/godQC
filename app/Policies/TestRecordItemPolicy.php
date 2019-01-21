<?php

namespace App\Policies;

use App\User;
use App\TestRecordItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class TestRecordItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the testRecordItem.
     *
     * @param  \App\User $user
     * @param  \App\TestRecordItem $testRecordItem
     * @return mixed
     */
    public function view(User $user, TestRecordItem $testRecordItem)
    {
        return true;
    }

    /**
     * Determine whether the user can create testRecordItems.
     *
     * @param  \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the testRecordItem.
     *
     * @param  \App\User $user
     * @param  \App\TestRecordItem $testRecordItem
     * @return mixed
     */
    public function update(User $user, TestRecordItem $testRecordItem)
    {
        return !$testRecordItem->testRecord->is_archived || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the testRecordItem.
     *
     * @param  \App\User $user
     * @param  \App\TestRecordItem $testRecordItem
     * @return mixed
     */
    public function delete(User $user, TestRecordItem $testRecordItem)
    {
        return !$testRecordItem->testRecord->is_archived || $user->hasRole('admin');
    }
}
