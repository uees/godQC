<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\QCRecordItemRequest;
use App\Http\Resources\TestRecordItemResource;
use App\TestRecordItem;
use App\Http\Controllers\Controller;
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

    public function show(TestRecordItem $testRecordItem)
    {
        return TestRecordItemResource::make($testRecordItem);
    }

    public function update(QCRecordItemRequest $request, TestRecord $testRecord, $itemId)
    {
        $testRecordItem = $testRecord->items()->findOrFail($itemId);

        $testRecordItem->fill($request->all())
            ->save();

        return TestRecordItemResource::make($testRecordItem);
    }

    public function destroy($id)
    {
        if (TestRecordItem::destroy($id)) {
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }
}
