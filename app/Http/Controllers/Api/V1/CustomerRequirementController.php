<?php

namespace App\Http\Controllers\Api\V1;

use App\CustomerRequirement;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequirementRequest;
use App\Http\Resources\CustomerRequirementResource;

class CustomerRequirementController extends Controller
{
    public function store(CustomerRequirementRequest $request)
    {
        $requirement = new CustomerRequirement();

        $requirement->fill($request->all())
            ->save();

        return CustomerRequirementResource::make($requirement);
    }


    public function update(CustomerRequirementRequest $request, $id)
    {
        $customerRequirement = CustomerRequirement::findOrFail($id);
        $customerRequirement->fill($request->all())
            ->save();

        return CustomerRequirementResource::make($customerRequirement);
    }


    public function destroy($id)
    {
        if (CustomerRequirement::destroy($id)) {
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }
}
