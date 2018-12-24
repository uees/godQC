<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\QCSampled;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductBatchRequest;
use App\Http\Requests\ProductDisposeRequest;
use App\Http\Resources\ProductDisposeResource;
use App\Http\Resources\TestRecordResource;
use App\ProductDispose;
use App\TestRecord;
use Illuminate\Database\Eloquent\Builder;

class ProductDisposeController extends Controller
{
    // q, all, with, done, author, created_at, from_record_id, to_record_id, product_batch_id
    public function index()
    {
        $perPage = $this->perPage();

        $query = ProductDispose::query();

        if (\request()->filled('with')) {
            $query = $query->with(explode(',', request('with')));
        }

        $query = $this->parseWhere($query, ['author', 'created_at', 'from_record_id', 'to_record_id', 'product_batch_id']);

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

        if (\request()->filled('all')) {
            $results = $query->get();
        } else {
            $results = $query->paginate($perPage)->appends(request()->except('page'));
        }

        return ProductDisposeResource::collection($results);
    }


    // 创建处理办法
    public function store(ProductDisposeRequest $request)
    {
        $this->authorize('create', ProductDispose::class);

        $productDispose = new ProductDispose();
        $productDispose->fill($request->all());

        if (empty($productDispose->product_batch_id)) {
            $record = TestRecord::where('id', $request->get('from_record_id'))->firstOrFail();
            $productDispose->product_batch_id = $record->product_batch_id;
        }

        $productDispose->save();

        return ProductDisposeResource::make($productDispose);
    }


    public function show($id)
    {
        $query = ProductDispose::query();

        if (\request()->filled('with')) {
            $query = $query->with(explode(',', request('with')));
        }

        $productDispose = $query->findOrFail($id);

        return ProductDisposeResource::make($productDispose);
    }


    public function update(ProductDisposeRequest $request, $id)
    {
        $productDispose = ProductDispose::findOrFail($id);

        $this->authorize('update', $productDispose);

        $productDispose->fill($request->all())->save();

        return ProductDisposeResource::make($productDispose);
    }


    public function destroy($id)
    {
        $productDispose = ProductDispose::findOrFail($id);

        $this->authorize('delete', $productDispose);

        if ($productDispose->delete()){
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }

    // 查找指定批次的处理记录
    public function batchDispose(ProductBatchRequest $request)
    {
        $dispose = ProductDispose::with('batch')
            ->whereHas('batch', function (Builder $query) use ($request) {
                $batchNumber = $request->get('batch_number');
                $productName = $request->get('product_name');
                $type = $request->get('type');

                $query->where('product_name', $productName)
                    ->where('batch_number', $batchNumber)
                    ->where('type', $type)
                    ->where('is_done', false);
            })->first();

        if (!is_null($dispose)) {
            return ProductDisposeResource::make($dispose);
        }

        return $this->message('无处理记录');
    }

    public function sample(ProductDispose $productDispose)
    {
        $this->authorize('create', TestRecord::class);

        // step 1. 确定批次
        $batch = $productDispose->batch;

        // step 2. 创建空白检测表单
        $testRecord = (new TestRecord());
        $batch->testRecords()->save($testRecord);

        // step 3. 与处理记录进行关联
        $productDispose->recordTo()->associate($testRecord);
        $productDispose->is_done = true;
        $productDispose->save();

        // step 4. 创建检测项目
        $recordFrom = $productDispose->recordFrom;

        $items = [];
        foreach ($recordFrom->items as $item) {
            if ($item->conclusion == 'NG') {
                array_push($items, [
                    'item' => $item['item'],
                    'spec' => $item['spec'],
                    'memo' => $item['memo'],
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
