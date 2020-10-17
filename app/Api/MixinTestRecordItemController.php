<?php


namespace App\Api;

use App\Http\Resources\MixinTestRecordItemResource;
use App\MixinTestRecord;
use App\MixinTestRecordItem;
use Illuminate\Http\Request;


class MixinTestRecordItemController extends Controller
{
    public function index(MixinTestRecord $testRecord)
    {
        $testRecordItems = $testRecord->items();

        return MixinTestRecordItemResource::collection($testRecordItems);
    }

    public function store(Request $request, MixinTestRecord $testRecord)
    {
        // $this->authorize('create', TestRecordItem::class);

        $testRecordItem = new MixinTestRecordItem();
        $testRecordItem->fill($request->all());

        $testRecord->items()->save($testRecordItem);

        return MixinTestRecordItemResource::make($testRecordItem);
    }

    public function show(MixinTestRecord $testRecord, MixinTestRecordItem $testRecordItem)
    {
        if ($testRecord->items->contains($testRecordItem)) {
            return MixinTestRecordItemResource::make($testRecordItem);
        }

        abort(404);
    }

    public function update(Request $request, MixinTestRecord $testRecord, MixinTestRecordItem $testRecordItem)
    {
        // $this->authorize('update', $testRecordItem);

        if ($testRecord->items->contains($testRecordItem)) {
            // 创建 fake value
            if ($request->get('conclusion') == 'NG') {
                if (is_null($testRecordItem->spec)) {
                    $testRecordItem->fake_value = 'PASS';
                } elseif ($testRecordItem->spec['value_type'] == 'INFO') {
                    $testRecordItem->fake_value = 'PASS';
                } elseif ($testRecordItem->spec['value_type'] == 'NUMBER') {
                    $testRecordItem->fake_value = isset($testRecordItem->spec['data']['value']) && $testRecordItem->spec['data']['value']
                        ? $testRecordItem->spec['data']['value']
                        : 'PASS';
                } elseif ($testRecordItem->spec['value_type'] == 'RANGE') {
                    if (isset($testRecordItem->spec['data']['min']) && $testRecordItem->spec['data']['min']) {
                        $testRecordItem->fake_value = $testRecordItem->spec['data']['min'];
                    } elseif (isset($testRecordItem->spec['data']['max']) && $testRecordItem->spec['data']['max']) {
                        $testRecordItem->fake_value = $testRecordItem->spec['data']['max'];
                    } else {
                        $testRecordItem->fake_value = 'PASS';
                    }
                }
            }

            $testRecordItem->fill($request->except('fake_value'))->save();

            return MixinTestRecordItemResource::make($testRecordItem);
        }

        abort(404);
    }

    public function destroy(MixinTestRecord $testRecord, MixinTestRecordItem $testRecordItem)
    {
        // $this->authorize('delete', $testRecordItem);

        if ($testRecord->items->contains($testRecordItem)) {
            if (MixinTestRecordItem::destroy($testRecordItem->id)) {
                return $this->response()->noContent();
            }

            return $this->response()->failed('操作失败');
        }

        abort(404);
    }
}