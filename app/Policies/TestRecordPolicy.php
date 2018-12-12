<?php

namespace App\Policies;

use App\User;
use App\TestRecord;
use Illuminate\Auth\Access\HandlesAuthorization;

class TestRecordPolicy
{
    use HandlesAuthorization;

    public function view(User $user, TestRecord $testRecord)
    {
        //
    }

    public function create(User $user)
    {
        //
    }

    public function update(User $user, TestRecord $testRecord)
    {
        //
    }

    public function delete(User $user, TestRecord $testRecord)
    {
        return is_null($testRecord->said_package_at) || $user->hasRole('admin');
    }
}
