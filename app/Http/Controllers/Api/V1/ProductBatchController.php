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
        $query = ProductBatch::query();

        if (\request()->filled('q')) {
            $name_condition = queryCondition('product_name', \request('q'));
            $batch_condition = queryCondition('batch_number', \request('q'));

            $query->where($name_condition)
                ->orWhere($batch_condition);
        }

        $query->orderBy($this->sortBy(), $this->order());

        $pagination = $query->paginate($perPage)->appends(request()->except('page'));

        return ProductBatchResource::collection($pagination);
    }


    public function store(ProductBatchRequest $request)
    {
        $this->authorize('create', ProductBatch::class);

        $productBatch = new ProductBatch();

        $productBatch->fill($request->all())
            ->save();

        return ProductBatchResource::make($productBatch);
    }


    public function show(ProductBatch $productBatch)
    {
        return ProductBatchResource::make($productBatch);
    }


    public function update(ProductBatchRequest $request, $id)
    {
        $productBatch = ProductBatch::findOrFail($id);

        $this->authorize('update', $productBatch);

        $productBatch->fill($request->all())->save();

        return ProductBatchResource::make($productBatch);
    }


    public function destroy($id)
    {
        $productBatch = ProductBatch::findOrFail($id);

        $this->authorize('delete', $productBatch);

        if ($productBatch->delete()) {
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }
}
