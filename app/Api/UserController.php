<?php

namespace App\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\User;

class UserController extends Controller
{
    public function index()
    {
        $perPage = $this->perPage();
        $query = User::query();

        if ($search = \request('q')) {
            $query->where(function ($query) use ($search) {
                $name_condition = queryCondition('name', $search);
                $email_condition = queryCondition('email', $search);

                $query->where($name_condition)
                    ->orWhere($email_condition);
            });
        }

        $query->orderBy($this->sortBy(), $this->order());

        $pagination = $query
            ->paginate($perPage)
            ->appends(request()->except('page'));

        return UserResource::collection($pagination);
    }


    public function store(UserRequest $request)
    {
        $this->authorize('create', User::class);

        $user = new User();

        $user->fill($request->all())->save();

        return UserResource::make($user);
    }


    public function show(User $user)
    {
        return UserResource::make($user);
    }


    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('update', $user);

        $user->fill($request->all())->save();

        return UserResource::make($user);
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $this->authorize('delete', $user);

        if ($user->delete()) {
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }
}
