<?php

namespace App\Policies;

use App\User;
use App\TestRecord;
use Illuminate\Auth\Access\HandlesAuthorization;

class TestRecordPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the testRecord.
     *
     * @param  \App\User  $user
     * @param  \App\TestRecord  $testRecord
     * @return mixed
     */
    public function view(User $user, TestRecord $testRecord)
    {
        //
    }

    /**
     * Determine whether the user can create testRecords.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the testRecord.
     *
     * @param  \App\User  $user
     * @param  \App\TestRecord  $testRecord
     * @return mixed
     */
    public function update(User $user, TestRecord $testRecord)
    {
        //
    }

    /**
     * Determine whether the user can delete the testRecord.
     *
     * @param  \App\User  $user
     * @param  \App\TestRecord  $testRecord
     * @return mixed
     */
    public function delete(User $user, TestRecord $testRecord)
    {
        //
    }
}
