<?php

namespace App\Api;

use App\CustomerRequirement;
use App\Http\Requests\CustomerRequirementRequest;
use App\Http\Resources\CustomerRequirementResource;

class CustomerRequirementController extends Controller
{
    public function store(CustomerRequirementRequest $request)
    {
        $this->authorize('create', \App\Customer::class);

        $requirement = new CustomerRequirement();

        $requirement->fill($request->all())
            ->save();

        return CustomerRequirementResource::make($requirement);
    }


    public function update(CustomerRequirementRequest $request, $id)
    {
        $this->authorize('create', \App\Customer::class);

        $customerRequirement = CustomerRequirement::findOrFail($id);
        $customerRequirement->fill($request->all())->save();

        return CustomerRequirementResource::make($customerRequirement);
    }


    public function destroy($id)
    {
        $this->authorize('create', \App\Customer::class);

        if (CustomerRequirement::destroy($id)) {
            return $this->response()->noContent();
        }

        return $this->response()->failed('操作失败');
    }
}
