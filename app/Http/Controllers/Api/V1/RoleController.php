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
        $name_condition = queryCondition('name', \request('q'));

        $roles = Role::where($name_condition)->get(); // 直接获取所有

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


    public function update(RoleRequest $request, Role $role)
    {
        $role->fill($request->all())->save();

        return RoleResource::make($role);
    }


    public function destroy(Role $role)
    {
        $role->delete();

        return $this->noContent();
    }
}
