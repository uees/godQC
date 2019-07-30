<?php


namespace App\Api;

use App\MixinTestRecord;
use App\Product;
use App\TestWay;
use App\Http\Resources\MixinTestRecordResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;


class MixinTestRecordController extends Controller
{
    // query params: testing, tested, conclusion, show_reality, all
    public function index()
    {
        $perPage = $this->perPage();

        $query = MixinTestRecord::query();

        $this->loadRelByQuery($query);
        $this->parseFields($query);
        $this->parseWhere($query, [
            'conclusion', 'show_reality', 'created_at',
            'part_a_name', 'part_a_batch', 'part_b_name', 'part_b_batch'
        ]);

        if (\request('has_memo') == 'true') {
            $query->whereNotNull('memo');
        }

        if (\request('testing') == '1') {
            $query->where('is_archived', 0);
        } elseif (\request('tested') == '1') {
            $query->where('is_archived', 1);
        }

        // search
        if ($search = \request('q')) {
            $query->where(function (Builder $query) use ($search) {
                $query->where(queryCondition('memo', $search))
                    ->orWhere(queryCondition('part_a_name', $search))
                    ->orWhere(queryCondition('part_a_batch', $search))
                    ->orWhere(queryCondition('part_b_name', $search))
                    ->orWhere(queryCondition('part_b_batch', $search));
            });
        }

        $query->orderBy($this->sortBy(), $this->order());

        if (\request('all')) {
            $records = $query->get();
        } else {
            $records = $query->paginate($perPage)->appends(request()->except('page'));
        }

        return MixinTestRecordResource::collection($records);
    }

    // with=product,items
    public function show($id)
    {
        $query = MixinTestRecord::query();

        $this->loadRelByQuery($query);

        $testRecord = $query->findOrFail($id);

        return MixinTestRecordResource::make($testRecord);
    }

    public function update(Request $request, $id)
    {
        $testRecord = MixinTestRecord::with('product')
            ->where('id', $id)
            ->firstOrFail();

        if ($request->filled('do_update_items')) {
            $items = $request->get('items');
            $items = is_array($items) ? $items : json_decode($items);

            foreach ($items as $item) {
                $testRecord->items()
                    ->where('id', $item['id'])
                    ->update([
                        'value' => $item['value'],
                        'fake_value' => $item['fake_value'],
                        'tester' => $item['tester'],
                        'conclusion' => $item['conclusion'],
                        'memo' => $item['memo'],
                    ]);
            }

            $testRecord->load('items');  // 加载关系
        }

        if ($testRecord->update($request->all())) {
            return MixinTestRecordResource::make($testRecord);
        }

        return $this->response()->failed('操作失败');
    }

    public function destroy($id)
    {
        $testRecord = MixinTestRecord::where('id', $id)->firstOrFail();

        // $this->authorize('delete', $testRecord);

        if ($testRecord->delete()) {
            $testRecord->items()->delete();
            return $this->response()->noContent();
        }

        return $this->response()->failed('操作失败');
    }

    // 取样
    public function sample(Request $request)
    {
        // $this->authorize('create', TestRecord::class);

        $productName = $request->get('product_name');
        $productNameSuffix = $request->get('product_name_suffix');
        $product = Product::where('internal_name', $productName)->firstOrFail();
        $part_a_name = $productName . $productNameSuffix;

        // 创建空白检测表单
        $testRecord = new MixinTestRecord();
        $testRecord->product()->associate($product);
        $testRecord->part_a_name = $part_a_name;
        $testRecord->fill($request->only([
            'part_a_batch',
            'part_b_name',
            'part_b_batch',
        ]));
        $testRecord->save();

        // 获取创建检测项目
        $test_way = $this->makeTestWay($product);

        $items = [];
        foreach ($test_way as $item) {
            array_push($items, [
                'name' => $item['name'],
                'spec' => $item['spec'],
            ]);
        }

        $testRecord->items()->createMany($items);

        // loading relationships
        $testRecord->load('items');

        return MixinTestRecordResource::make($testRecord);
    }

    public function archive(MixinTestRecord $testRecord)
    {
        // $this->authorize('update', $testRecord);

        if (empty($testRecord->testers)) {
            return $this->response()->failed('请输入检测员');
        }

        foreach ($testRecord->items as $item) {
            if (isset($item->spec['required']) && $item->spec['required'] == false) {
                continue;
            }

            if (is_null($item->value) || $item->value == '') {
                return $this->response()->failed("检测项目 '{$item['item']}' 的值未填写， 不能归档");
            }
        }

        if (empty($testRecord->conclusion)) {
            $testRecord->conclusion = 'PASS';
        }

        if ($testRecord->conclusion == 'NG' && is_null($testRecord->memo)) {
            return $this->response()->failed('此批次不合格，并且还未备注意见，不能归档');
        }

        // 留空就表示合格, 这里帮忙填入PASS
        $testRecord->items()
            ->whereNull('conclusion')
            ->update(['conclusion' => 'PASS']);

        $testRecord->is_archived = true;

        if ($testRecord->save()) {
            return MixinTestRecordResource::make($testRecord);
        }

        return $this->response()->failed('操作失败');
    }

    public function cancelArchived(MixinTestRecord $testRecord)
    {
        // $this->authorize('update', $testRecord);

        $testRecord->is_archived = false;

        if ($testRecord->save()) {
            return MixinTestRecordResource::make($testRecord);
        }

        return $this->response()->failed('操作失败');
    }

    public function testDone(MixinTestRecord $testRecord)
    {
        // $this->authorize('update', $testRecord);

        $testRecord->completed_at = now();

        if ($testRecord->save()) {
            return MixinTestRecordResource::make($testRecord);
        }

        return $this->response()->failed('操作失败');
    }

    protected function makeTestWay(Product $product)
    {
        // 混合粘度要求
        $viscosity_unit = $product->meta('viscosity_unit');  // 粘度单位
        if ($label_viscosity = (int)$product->label_viscosity) {
            $viscosity_width = (int)$product->viscosity_width ?: 5;
            $mixViscosity = [
                'name' => '混合粘度',
                'method' => '',
                'method_id' => '',
                'spec' => [
                    'is_show' => true,
                    'required' => true,
                    'value_type' => 'RANGE',
                    'data' => [
                        'min' => floatval($label_viscosity - $viscosity_width),
                        'max' => floatval($label_viscosity + $viscosity_width),
                        'value' => '',
                        'unit' => $viscosity_unit,
                        'memo' => '',
                    ],
                ],
            ];
        } else {
            $mixViscosity = [
                'name' => '混合粘度',
                'method' => '',
                'method_id' => '',
                'spec' => [
                    'is_show' => true,
                    'required' => true,
                    'value_type' => 'INFO',
                    'data' => [
                        'min' => null,
                        'max' => null,
                        'value' => '记录数据',
                        'unit' => '',
                        'memo' => '',
                    ],
                ],
            ];
        }
        $test_way = [$mixViscosity];

        // 获取检测要求
        $testWay = TestWay::where('name', '感光阻焊油混合测试项目')->firstOrFail();
        $this->mergeTestWay($test_way, $testWay->way);

        // 合并产品的检测项目
        if ($product->testWay) {
            $test_way = $this->mergeTestWay($test_way, $product->testWay->way);
        }

        return $test_way;
    }

    /**
     * @param array $way
     * @param string $itemName
     * @return bool
     */
    protected function hasItem(array $way, $itemName)
    {
        $result = false;
        foreach ($way as $item) {
            if ($item['name'] == $itemName) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * @param array $way
     * @param string $itemName
     * @return mixed
     */
    protected function getItem(array $way, $itemName)
    {
        foreach ($way as $item) {
            if ($item['name'] == $itemName) {
                return $item;
            }
        }
    }

    /**
     * @param array $origin
     * @param array $newer
     * @return array
     */
    protected function mergeTestWay(array &$origin, array $newer)
    {
        // 覆盖操作
        foreach ($origin as $key => $origin_item) {
            $inNewerItem = $this->getItem($newer, $origin_item['name']);
            if (!is_null($inNewerItem)) {
                $origin[$key] = $inNewerItem;
            }
        }

        // 新增操作
        foreach ($newer as $newer_item) {
            if (!$this->hasItem($origin, $newer_item['name'])) {
                array_push($origin, $newer_item);
            }
        }

        return $origin;
    }
}