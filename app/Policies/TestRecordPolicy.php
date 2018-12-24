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
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, TestRecord $testRecord)
    {
        return !$testRecord->is_archived || $user->hasRole('admin');
    }

    public function delete(User $user, TestRecord $testRecord)
    {
        return !$testRecord->is_archived || $user->hasRole('admin');
    }
}
