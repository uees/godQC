<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductDisposeRequest;
use App\Http\Resources\ProductDisposeResource;
use App\ProductDispose;
use Illuminate\Database\Query\Builder;

class ProductDisposeController extends Controller
{
    public function index()
    {
        $perPage = $this->perPage();

        $query = ProductDispose::query();

        if (\request()->filled('q')) {
            $name_condition = queryCondition('product_name', \request('q'));
            $batch_condition = queryCondition('batch_number', \request('q'));
            $method_condition = queryCondition('method', \request('q'));

            $query = $query->where($method_condition)
                ->orWhereHas('batch', function (Builder $query) use ($name_condition, $batch_condition) {
                    $query->where($name_condition)
                        ->orWhere($batch_condition);
                });
        }

        $query = $query->orderBy($this->sortBy(), $this->order());

        $pagination = $query->paginate($perPage)->appends(request()->except('page'));

        return ProductDisposeResource::collection($pagination);
    }


    // 创建处理办法
    public function store(ProductDisposeRequest $request)
    {
        $productDispose = new ProductDispose();

        $productDispose->fill($request->all())->save();

        return ProductDisposeResource::make($productDispose);
    }


    public function show(ProductDispose $productDispose)
    {
        return ProductDisposeResource::make($productDispose);
    }


    public function update(ProductDisposeRequest $request, ProductDispose $productDispose)
    {
        $productDispose->fill($request->all())->save();

        return ProductDisposeResource::make($productDispose);
    }


    public function destroy($id)
    {
        if (ProductDispose::destroy($id)) {
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }
}
