<?php


namespace App\Policies;

use App\User;
use App\A9060PatternTest;
use Illuminate\Auth\Access\HandlesAuthorization;

class A9060PatternTestPolicy
{
    use HandlesAuthorization;

    public function view(User $user, A9060PatternTest $patternTest)
    {
        return true;
    }

    public function create(User $user)
    {
        return $user->hasRole('fqc');
    }

    public function update(User $user, A9060PatternTest $patternTest)
    {
        return $user->hasRole('fqc');
    }

    public function delete(User $user, A9060PatternTest $patternTest)
    {
        return $user->hasRole('fqc');
    }
}