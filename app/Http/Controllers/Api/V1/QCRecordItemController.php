<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\QCRecordItemRequest;
use App\Http\Resources\TestRecordItemResource;
use App\Http\Controllers\Controller;
use App\TestRecordItem;
use App\TestRecord;

class QCRecordItemController extends Controller
{
    public function index(TestRecord $testRecord)
    {
        $testRecordItems = $testRecord->items();

        return TestRecordItemResource::collection($testRecordItems);
    }

    public function store(QCRecordItemRequest $request, TestRecord $testRecord)
    {
        $testRecordItem = new TestRecordItem();
        $testRecordItem->fill($request->all());

        $testRecord->items()->save($testRecordItem);

        return TestRecordItemResource::make($testRecordItem);
    }

    public function show(TestRecord $testRecord, TestRecordItem $testRecordItem)
    {
        if ($testRecord->items->contains($testRecordItem)) {
            return TestRecordItemResource::make($testRecordItem);
        }

        abort(404);
    }

    public function update(QCRecordItemRequest $request, TestRecord $testRecord, TestRecordItem $testRecordItem)
    {
        if ($testRecord->items->contains($testRecordItem)) {
            // 创建 fake value
            if ($request->get('conclusion') == 'NG') {
                if (is_null($testRecordItem->spec)) {
                    $testRecordItem->fake_value = 'PASS';
                } elseif ($testRecordItem->spec['value_type'] == 'INFO') {
                    $testRecordItem->fake_value = 'PASS';
                } elseif ($testRecordItem->spec['value_type'] == 'NUMBER') {
                    $testRecordItem->fake_value = $testRecordItem->spec['data']['value'];
                } elseif ($testRecordItem->spec['value_type'] == 'RANGE') {
                    $testRecordItem->fake_value = $testRecordItem->spec['data']['min']
                        ?: $testRecordItem->spec['data']['max'];
                }
            }

            $testRecordItem->fill($request->all())->save();

            return TestRecordItemResource::make($testRecordItem);
        }

        abort(404);
    }

    public function destroy(TestRecord $testRecord, TestRecordItem $testRecordItem)
    {
        if ($testRecord->items->contains($testRecordItem)) {
            if (TestRecordItem::destroy($testRecordItem->id)) {
                return $this->noContent();
            }

            return $this->failed('操作失败');
        }

        abort(404);
    }
}
