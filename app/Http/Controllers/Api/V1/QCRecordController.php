<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TestRecordResource;
use App\TestRecord;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class QCRecordController extends Controller
{
    public function index()
    {
        $perPage = $this->perPage();
        $name_condition = queryCondition('product_name', \request('q'));
        $batch_condition = queryCondition('batch_number', \request('q'));

        $query = TestRecord::whereHas('batch', function (Builder $query) use ($name_condition, $batch_condition) {
            $query->where($name_condition)
                ->orWhere($batch_condition);
        });

        $query = $this->parseWhere($query, ['created_at', 'conclusion']);

        $pagination = $query->paginate($perPage)->appends(request()->except('page'));

        return TestRecordResource::collection($pagination);
    }

    // 取样
    public function store(Request $request)
    {
        // todo step 1. 创建批次

        // todo step 2. 创建空白检测表单
        $testRecord = new TestRecord();

        $testRecord->fill($request->all())->save();

        return TestRecordResource::make($testRecord);
    }


    public function show($id)
    {
        $testRecord = TestRecord::with(['batch', 'items'])->find($id);

        return TestRecordResource::make($testRecord);
    }


    public function update(Request $request, TestRecord $testRecord)
    {
        $testRecord->fill($request->all())->save();

        // todo update record_items

        return TestRecordResource::make($testRecord);
    }


    public function destroy(TestRecord $testRecord)
    {
        $testRecord->delete();

        $testRecord->items()->delete();

        return $this->noContent();
    }
}
