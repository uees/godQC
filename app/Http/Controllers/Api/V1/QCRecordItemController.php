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
