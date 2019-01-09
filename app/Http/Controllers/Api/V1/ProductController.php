<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Controllers\Controller;
use App\Product;
use App\TestWay;

class ProductController extends Controller
{
    // sort_by,order,with,q
    public function index()
    {
        $perPage = $this->perPage();
        $query = Product::query();

        if (\request()->filled('with')) {
            $query = $query->with(explode(',', request('with')));
        }

        if (\request()->filled('q')) {
            $internal_name_condition = queryCondition('internal_name', \request('q'));
            $query = $query->where($internal_name_condition);
        }

        $pagination = $query->orderBy($this->sortBy(), $this->order())
            ->paginate($perPage)
            ->appends(request()->except('page'));

        return ProductResource::collection($pagination);
    }


    public function store(ProductRequest $request)
    {
        $this->authorize('create', Product::class);

        $product = new Product();

        $product->fill($request->all())->save();

        // 加载关系
        if ($request->filled('with')) {
            foreach (explode(',', $request->get('with')) as $ref) {
                $product->{$ref};
            }
        }

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

        $this->authorize('update', $product);

        $product->fill($request->all())->save();

        // 加载关系
        if ($request->filled('with')) {
            foreach (explode(',', $request->get('with')) as $ref) {
                $product->{$ref};
            }
        }

        return ProductResource::make($product);
    }


    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $this->authorize('delete', $product);

        if ($product->delete()) {
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }

    public function selectTestWay(Product $product)
    {
        $this->authorize('update', $product);

        $testWayId = request('test_way_id');

        if (empty($testWayId) || (int)$testWayId == 0) {
            $testWay = $product->testWay;

            $product->testWay()->dissociate();
            $product->save();

            if (!$testWay->products()->exists() && !$testWay->categories()->exists()) {
                $testWay->delete();
            }

            return $this->noContent();
        }

        if (TestWay::where('id', $testWayId)->exists()) {
            $product->testWay()->associate($testWayId);
            $product->save();

            return $this->noContent();
        }

        return $this->failed('操作失败');
    }
}
