<?php

namespace App\Api;

use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Role;

class RoleController extends Controller
{
    public function index()
    {
        $query = Role::query();

        if ($search = \request('q')) {
            $name_condition = queryCondition('name', $search);

            $query->where($name_condition);
        }

        $roles = $query->get(); // 直接获取所有

        return RoleResource::collection($roles);
    }


    public function store(RoleRequest $request)
    {
        $this->authorize('create', Role::class);

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

        $this->authorize('update', $role);

        $role->fill($request->all())->save();

        return RoleResource::make($role);
    }


    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $this->authorize('delete', $role);

        if ($role->delete()) {
            return $this->response()->noContent();
        }

        return $this->response()->failed('操作失败');
    }
}
