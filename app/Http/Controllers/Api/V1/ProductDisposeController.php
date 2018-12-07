<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\QCSampled;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductDisposeRequest;
use App\Http\Resources\ProductDisposeResource;
use App\Http\Resources\TestRecordResource;
use App\ProductDispose;
use App\TestRecord;
use Illuminate\Database\Query\Builder;

class ProductDisposeController extends Controller
{
    // q, with, done, author, created_at
    public function index()
    {
        $perPage = $this->perPage();

        $query = ProductDispose::query();

        if (\request()->filled('with')) {
            $query = $query->with(explode(',', request('with')));
        }

        $query = $this->parseWhere($query, ['author', 'created_at']);

        if (\request()->filled('done')) {
            $query = $query->where('is_done', 1);
        }

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


    public function update(ProductDisposeRequest $request, $id)
    {
        $productDispose = ProductDispose::findOrFail($id);
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

    public function sample(ProductDispose $productDispose) {
        // step 1. 确定批次
        $batch = $productDispose->batch;

        // step 2. 创建空白检测表单
        $testRecord = (new TestRecord());
        $batch->testRecords()->save($testRecord);

        // step 3. 创建检测项目
        $recordFrom = $productDispose->recordFrom;

        $items = [];
        foreach ($recordFrom->items as $item) {
            if ($item->conclusion == 'NG') {
                array_push($items, [
                    'item' => $item['name'],
                    'spec' => $item['spec'],
                    'value' => '',
                ]);
            }
        }
        $testRecord->items()->createMany($items);

        // loading relationships
        $testRecord->batch;
        $testRecord->items;

        // 触发事件
        event(new QCSampled($batch, $testRecord));

        return TestRecordResource::make($testRecord);
    }
}
