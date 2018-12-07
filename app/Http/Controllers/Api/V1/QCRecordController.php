<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\QCSampled;
use App\Events\RecordDeleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductBatchRequest;
use App\Http\Resources\TestRecordResource;
use App\Product;
use App\ProductBatch;
use App\TestRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class QCRecordController extends Controller
{
    // query params: type, testing, tested, conclusion, test_times, all
    public function index()
    {
        $perPage = $this->perPage();

        $query = TestRecord::query();

        if (\request()->filled('with')) {
            $query = $query->with(explode(',', request('with')));
        }

        $query = $this->parseFields($query);
        $query = $this->parseWhere($query, ['conclusion', 'test_times', 'created_at']);

        if (\request()->filled('testing')) {
            $query = $query->whereNull('said_package_at');
        }

        if (\request()->filled('tested')) {
            $query = $query->whereNotNull('said_package_at');
        }

        if (\request()->filled('type')) {
            $query = $query->whereHas('batch', function (Builder $query) {
                $query->where('type', \request('type'));
            });
        }

        if (\request()->filled('q')) {
            $query = $query->whereHas('batch', function (Builder $query) {
                $name_condition = queryCondition('product_name', \request('q'));
                $batch_condition = queryCondition('batch_number', \request('q'));

                $query->where($name_condition)
                    ->orWhere($batch_condition);
            });
        }

        $query = $query->orderBy($this->sortBy(), $this->order());

        if (\request()->filled('all')) {
            $records = $query->get();
        } else {
            $records = $query->paginate($perPage)->appends(request()->except('page'));
        }

        return TestRecordResource::collection($records);
    }

    public function store()
    {
        abort(404);
    }


    // with=batch,items
    public function show($id)
    {
        $query = TestRecord::query();

        if (\request()->filled('with')) {
            $query = $query->with(explode(',', request('with')));
        }

        $testRecord = $query->findOrFail($id);

        return TestRecordResource::make($testRecord);
    }


    public function update(Request $request, $id)
    {
        $testRecord = TestRecord::findOrFail($id);

        if ($testRecord->update($request->all())) {
            return TestRecordResource::make($testRecord);
        }

        return $this->failed('操作失败');
    }


    public function destroy($id)
    {
        $testRecord = TestRecord::find($id);

        if (TestRecord::destroy($id)) {
            $testRecord->items()->delete();

            event(new RecordDeleted($testRecord->batch, $testRecord));

            return $this->noContent();
        }

        return $this->failed('操作失败');
    }


    // 取样
    public function sample(ProductBatchRequest $request)
    {
        $productName = $request->get('product_name');
        $batchNumber = $request->get('batch_number');
        $type = $request->get('type');

        // step 1. 创建批次
        $batch = ProductBatch::where('product_name', $productName)
            ->where('batch_number', $batchNumber)
            ->first();

        if (is_null($batch)) {
            $batch = ProductBatch::create([
                'product_name' => $productName,
                'batch_number' => $batchNumber,
                'type' => $type,
            ]);
        }

        // step 2. 创建空白检测表单
        $testRecord = (new TestRecord());

        $batch->testRecords()->save($testRecord);

        // step 3. 创建检测项目
        // make 检测项目
        $product = Product::where('internal_name', $productName)->firstOrFail();
        $category = $product->category;

        $test_way = [];
        if ($category->testWays()->count()) {
            $test_way = $category->testWays[0]->way;
        }

        if ($product->testWays()->count()) {
            $test_way = array_merge($test_way, $product->testWays[0]->way);
        }

        $items = [];
        foreach ($test_way as $item) {
            array_push($items, [
                'item' => $item['name'],
                'spec' => $item['spec'],
                'value' => '',
            ]);
        }
        if (!empty($items)) {
            $testRecord->items()->createMany($items);
        }

        // loading relationships
        $testRecord->batch;
        $testRecord->items;

        // 触发事件
        event(new QCSampled($batch, $testRecord));

        return TestRecordResource::make($testRecord);
    }

    public function sayPackage(TestRecord $testRecord)
    {
        if ($testRecord->conclusion == 'NG') {
            if (is_null($testRecord->willDispose)) {
                return $this->failed('此批次不合格，并且还未提交处理意见，写装失败');
            }
        } elseif (empty($testRecord->conclusion)) {
            return $this->failed('检测完了才能写装');
        }

        $testRecord->said_package_at = now();

        if ($testRecord->save()) {
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }

    public function testDone(TestRecord $testRecord)
    {
        $testRecord->completed_at = now();
        $testRecord->conclusion = \request('conclusion');

        if ($testRecord->save()) {
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }
}
