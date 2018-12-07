<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Controllers\Controller;
use App\Product;
use App\TestWay;

class ProductController extends Controller
{
    // sort_by,order
    public function index()
    {
        $perPage = $this->perPage();
        $query = Product::query();

        if (\request()->filled('q')) {
            $internal_name_condition = queryCondition('internal_name', \request('q'));
            $market_name_condition = queryCondition('market_name', \request('q'));

            $query = $query->where($internal_name_condition)
                ->orWhere($market_name_condition);
        }

        $pagination = $query->orderBy($this->sortBy(), $this->order())
            ->paginate($perPage)
            ->appends(request()->except('page'));

        return ProductResource::collection($pagination);
    }


    public function store(ProductRequest $request)
    {
        $product = new Product();

        $product->fill($request->all())
            ->save();

        return ProductResource::make($product);
    }


    public function show($id)
    {
        $query = Product::query();

        if (\request()->filled('with')) {
            $query = $query->with(explode(',', request('with')));
        }

        $product = $query->findOrFail($id);

        return ProductResource::make($product);
    }


    public function update(ProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->fill($request->all())
            ->save();

        return ProductResource::make($product);
    }


    public function destroy($id)
    {
        if (Product::destroy($id)) {
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }

    public function selectTestWay(Product $product)
    {
        $testWayId = request('test_way_id');

        if (TestWay::where('id', $testWayId)->exists()) {
            $product->testWays()->sync([$testWayId]);

            return $this->noContent();
        }

        return $this->failed('操作失败');
    }
}
