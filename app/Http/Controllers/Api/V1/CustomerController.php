<?php

namespace App\Http\Controllers\Api\V1;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;

class CustomerController extends Controller
{
    public function index()
    {
        $perPage = $this->perPage();

        $query = Customer::query();

        if (\request()->filled('q')) {
            $name_condition = queryCondition('name', \request('q'));
            $query = $query->where($name_condition);
        }

        $pagination = $query->paginate($perPage)->appends(request()->except('page'));

        return CustomerResource::collection($pagination);
    }


    public function store(CustomerRequest $request)
    {
        $customer = new Customer();

        $customer->fill($request->all())
            ->save();

        return CustomerResource::make($customer);
    }


    public function show($id)
    {
        $customer = Customer::with(['requirements', 'products'])
            ->where('id', $id)
            ->firstOrFail();

        return CustomerResource::make($customer);
    }


    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->fill($request->all())->save();

        return CustomerResource::make($customer);
    }


    public function destroy(Customer $customer)
    {
        $customer->delete();

        return $this->noContent();
    }

    public function selectProducts()
    {
        // todo
    }
}
