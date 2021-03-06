<?php

namespace App\Api;

use App\Customer;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;

class CustomerController extends Controller
{
    public function index()
    {
        $perPage = $this->perPage();

        $query = Customer::query();

        if ($search = \request('q')) {
            $name_condition = queryCondition('name', $search);
            $query->where($name_condition);
        }

        $query->orderBy($this->sortBy(), $this->order());

        $pagination = $query->paginate($perPage)->appends(request()->except('page'));

        return CustomerResource::collection($pagination);
    }


    public function store(CustomerRequest $request)
    {
        $this->authorize('create', Customer::class);

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


    public function update(CustomerRequest $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $this->authorize('update', $customer);

        $customer->fill($request->all())->save();

        return CustomerResource::make($customer);
    }


    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        $this->authorize('delete', $customer);

        if ($customer->delete()) {
            return $this->response()->noContent();
        }

        return $this->response()->failed('操作失败');
    }

    public function selectProducts(Customer $customer)
    {
        $this->authorize('update', $customer);

        $product_ids = request('product_ids', []);
        $product_ids = is_array($product_ids) ? $product_ids : explode(',', $product_ids);

        $customer->products()->sync($product_ids);

        return $this->response()->noContent();
    }

    public function selectProduct(Customer $customer)
    {
        $this->authorize('update', $customer);

        if (request()->filled('product_id')) {
            $customer->products()->attach(request('product_id'));
        }

        return $this->response()->noContent();
    }
}
