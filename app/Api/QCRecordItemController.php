<?php

namespace App\Api;

use App\Http\Requests\QCRecordItemRequest;
use App\Http\Resources\TestRecordItemResource;
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
        $this->authorize('create', TestRecordItem::class);

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
        $this->authorize('update', $testRecordItem);

        if ($testRecord->items->contains($testRecordItem)) {
            // 创建 fake value
            if ($request->get('conclusion') == 'NG') {
                $testRecordItem->fake_value = TestRecordItem::makeFakeValue($testRecordItem->spec);
            }

            $testRecordItem->fill($request->except('fake_value'))->save();

            return TestRecordItemResource::make($testRecordItem);
        }

        abort(404);
    }

    public function destroy(TestRecord $testRecord, TestRecordItem $testRecordItem)
    {
        $this->authorize('delete', $testRecordItem);

        if ($testRecord->items->contains($testRecordItem)) {
            if (TestRecordItem::destroy($testRecordItem->id)) {
                return $this->response()->noContent();
            }

            return $this->response()->failed('操作失败');
        }

        abort(404);
    }
}
