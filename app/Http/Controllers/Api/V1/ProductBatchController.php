<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductBatchRequest;
use App\Http\Resources\ProductBatchResource;
use App\ProductBatch;

class ProductBatchController extends Controller
{
    public function index()
    {
        $perPage = $this->perPage();
        $name_condition = queryCondition('product_name', \request('q'));
        $batch_condition = queryCondition('batch_number', \request('q'));

        $pagination = ProductBatch::where($name_condition)
            ->orWhere($batch_condition)
            ->paginate($perPage)
            ->appends(request()->except('page'));

        return ProductBatchResource::collection($pagination);
    }


    public function store(ProductBatchRequest $request)
    {
        $productBatch = new ProductBatch();

        $productBatch->fill($request->all())
            ->save();

        return ProductBatchResource::make($productBatch);
    }


    public function show(ProductBatch $productBatch)
    {
        return ProductBatchResource::make($productBatch);
    }


    public function update(ProductBatchRequest $request, ProductBatch $productBatch)
    {
        $productBatch->fill($request->all())
            ->save();

        return ProductBatchResource::make($productBatch);
    }


    public function destroy(ProductBatch $productBatch)
    {
        $productBatch->delete();

        return $this->noContent();
    }
}
