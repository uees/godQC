<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductBatchRequest;
use App\Http\Resources\TestRecordResource;
use App\Product;
use App\ProductBatch;
use App\TestRecord;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class QCRecordController extends Controller
{
    // type, testing, conclusion, test_times
    public function index()
    {
        $perPage = $this->perPage();

        $query = TestRecord::query();

        $query = $this->parseFields($query);
        $query = $this->parseWhere($query, ['conclusion', 'test_times']);

        if (\request()->filled('with')) {
            $query = $query->with(explode(',', request('with')));
        }

        if (\request()->filled('testing')) {
            $query = $query->whereNull('said_package_at');
        }

        if (\request()->filled('tested')) {
            $query = $query->whereNotNull('said_package_at');
        }

        if (\request()->filled('type')) {
            $type = \request('type');
            $query = $query->whereHas('batch', function (Builder $query) use ($type) {
                $query->where('type', $type);
            });
        }

        if (\request()->filled('q')) {
            $name_condition = queryCondition('product_name', \request('q'));
            $batch_condition = queryCondition('batch_number', \request('q'));

            $query = $query->whereHas('batch', function (Builder $query) use ($name_condition, $batch_condition) {
                $query->where($name_condition)
                    ->orWhere($batch_condition);
            });
        }

        $query = $this->parseWhere($query, ['created_at', 'conclusion']);

        $pagination = $query->paginate($perPage)->appends(request()->except('page'));

        return TestRecordResource::collection($pagination);
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


    public function update(Request $request, TestRecord $testRecord)
    {
        $testRecord->fill($request->all())->save();

        return TestRecordResource::make($testRecord);
    }


    public function destroy($id)
    {
        $testRecord = TestRecord::find($id);

        if (TestRecord::destroy($id)) {
            $testRecord->items()->delete();

            return $this->noContent();
        }

        return $this->failed('操作失败');
    }


    // 取样
    public function sample(ProductBatchRequest $request, $type)
    {
        $productName = $request->get('product_name');
        $batchNumber = $request->get('batch_number');

        // step 1. 创建批次
        $batch = ProductBatch::firstOrCreate([
            'product_name' => $productName,
            'batch_number' => $batchNumber,
        ], ['type' => $type]);

        // step 2. 创建空白检测表单
        $testRecord = new TestRecord();
        $testRecord->fill([
            'test_times' => $batch->testRecords()->count() + 1,
        ]);

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
        $testRecord->items()->create($items);

        // loading relationships
        $testRecord->batch;
        $testRecord->items;

        return TestRecordResource::make($testRecord);
    }
}
