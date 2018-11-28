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


    public function update(CustomerRequirementRequest $request, CustomerRequirement $customerRequirement)
    {
        $customerRequirement->fill($request->all())
            ->save();

        return CustomerRequirementResource::make($customerRequirement);
    }


    public function destroy(CustomerRequirement $customerRequirement)
    {
        $customerRequirement->delete();

        return $this->noContent();
    }
}
