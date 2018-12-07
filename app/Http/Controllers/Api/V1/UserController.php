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
        $query = User::query();

        if (\request()->filled('q')) {
            $name_condition = queryCondition('name', \request('q'));
            $email_condition = queryCondition('email', \request('q'));

            $query = $query->where($name_condition)
                ->orWhere($email_condition);
        }

        $query = $query->orderBy($this->sortBy(), $this->order());

        $pagination = $query
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


    public function update(UserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $user->fill($request->all())->save();

        return UserResource::make($user);
    }


    public function destroy($id)
    {
        if (User::destroy($id)) {
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }

    public function testers()
    {
        return $this->respond([
            'data' => [
                [
                    'name' => '周菊明',
                ],
                [
                    'name' => '黄桂泉',
                ],
                [
                    'name' => '袁秀英',
                ],
                [
                    'name' => '谭姣姣',
                ],
                [
                    'name' => '王孟娇',
                ],
                [
                    'name' => '欧阳泽成',
                ],
                [
                    'name' => '谢秀琴',
                ],
                [
                    'name' => '周建兰',
                ],
                [
                    'name' => '万重阳',
                ],
            ],
        ]);
    }
}
