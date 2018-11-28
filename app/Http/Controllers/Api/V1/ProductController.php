<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Controllers\Controller;
use App\Product;

class ProductController extends Controller
{
    public function index()
    {
        $perPage = $this->perPage();
        $internal_name_condition = queryCondition('internal_name', \request('q'));
        $market_name_condition = queryCondition('market_name', \request('q'));

        $pagination = Product::where($internal_name_condition)
            ->orWhere($market_name_condition)
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


    public function show(Product $product)
    {
        return ProductResource::make($product);
    }


    public function update(ProductRequest $request, Product $product)
    {
        $product->fill($request->all())
            ->save();

        return ProductResource::make($product);
    }


    public function destroy(Product $product)
    {
        $product->delete();

        return $this->noContent();
    }

    public function selectTestWay()
    {
        // todo
    }
}
