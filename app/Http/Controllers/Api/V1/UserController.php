<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\User;

class UserController extends Controller
{
    public function index()
    {
        $perPage = $this->perPage();
        $name_condition = queryCondition('name', \request('q'));
        $email_condition = queryCondition('email', \request('q'));

        $pagination = User::where($name_condition)
            ->orWhere($email_condition)
            ->paginate($perPage)
            ->appends(request()->except('page'));

        return UserResource::collection($pagination);
    }


    public function store(UserRequest $request)
    {
        $user = new User();

        $user->fill($request->all())->save();

        return UserResource::make($user);
    }


    public function show(User $user)
    {
        return UserResource::make($user);
    }


    public function update(UserRequest $request, User $user)
    {
        $user->fill($request->all())->save();

        return UserResource::make($user);
    }


    public function destroy(User $user)
    {
        $user->delete();

        return $this->noContent();
    }
}
