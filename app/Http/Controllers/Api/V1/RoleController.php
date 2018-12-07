<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Role;

class RoleController extends Controller
{
    public function index()
    {
        $query = Role::query();

        if (\request()->filled('q')) {
            $name_condition = queryCondition('name', \request('q'));

            $query = $query->where($name_condition);
        }

        $roles = $query->get(); // 直接获取所有

        return RoleResource::collection($roles);
    }


    public function store(RoleRequest $request)
    {
        $role = new Role();

        $role->fill($request->all())->save();

        return RoleResource::make($role);
    }


    public function show(Role $role)
    {
        return RoleResource::make($role);
    }


    public function update(RoleRequest $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->fill($request->all())->save();

        return RoleResource::make($role);
    }


    public function destroy($id)
    {
        if (Role::destroy($id)) {
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }
}
