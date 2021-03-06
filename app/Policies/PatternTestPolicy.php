<?php

namespace App\Policies;

use App\User;
use App\PatternTest;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatternTestPolicy
{
    use HandlesAuthorization;

    public function view(User $user, PatternTest $patternTest)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->hasRole('fqc');
    }

    public function update(User $user, PatternTest $patternTest)
    {
        return $user->hasRole('fqc');
    }

    public function delete(User $user, PatternTest $patternTest)
    {
        return $user->hasRole('fqc');
    }
}
