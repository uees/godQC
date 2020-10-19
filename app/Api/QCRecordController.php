<?php

namespace App\Api;

use App\Events\QCSampled;
use App\Events\RecordDeleted;
use App\Http\Requests\ProductBatchRequest;
use App\Http\Resources\TestRecordResource;
use App\Category;
use App\Product;
use App\ProductBatch;
use App\TestRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class QCRecordController extends Controller
{
    // query params: type, testing, tested, conclusion, test_times, all, said_package, category
    public function index()
    {
        $perPage = $this->perPage();

        $query = TestRecord::query();

        $this->loadRelByQuery($query);
        $this->parseFields($query);
        $this->parseWhere($query, ['conclusion', 'show_reality', 'test_times', 'created_at']);

        if (request('said_package') == '1') {
            $query->whereNotNull('said_package_at');
        } elseif (request('said_package') == '0') {
            $query->whereNull('said_package_at');
        }

        if (\request('has_memo') == 'true') {
            $query->whereNotNull('memo');
        }

        if (\request('testing') == '1') {
            $query->where('is_archived', 0);
        } elseif (\request('tested') == '1') {
            $query->where('is_archived', 1);
        }

        if ($type = \request('type')) {
            $query->whereHas('batch', function (Builder $query) use ($type) {
                $query->where('type', $type);
            });
        }

        // 类别筛选支持
        $categoryId = \request('category');
        if ($categoryId && $categoryId != '0') {
            if ($category = Category::where('id', $categoryId)->first()) {
                $query->whereHas('batch', function (Builder $query) use ($category) {
                    $products = $category->products->pluck('internal_name');
                    $query->whereIn('product_name', $products);
                });
            }
        }

        // search
        if ($search = \request('q')) {
            $query->where(function (Builder $query) {
                $memo_condition = queryCondition('memo', \request('q'));
                $query->where($memo_condition)
                    ->orWhereHas('batch', function (Builder $query) {
                        $name_condition = queryCondition('product_name', \request('q'));
                        $batch_condition = queryCondition('batch_number', \request('q'));

                        $query->where($name_condition)
                            ->orWhere($batch_condition);
                    });
            });

        }

        $query->orderBy($this->sortBy(), $this->order());

        if (\request('all')) {
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

        $this->loadRelByQuery($query);

        $testRecord = $query->findOrFail($id);

        return TestRecordResource::make($testRecord);
    }


    public function update(Request $request, $id)
    {
        $testRecord = TestRecord::with('batch')
            ->where('id', $id)
            ->firstOrFail();

        // 有时要修改备注信息, 所以不再设权限
        // $this->authorize('update', $testRecord);

        if ($request->filled('batch')) {
            $batch = $request->get('batch');
            $batch = is_array($batch) ? $batch : json_decode($batch);

            $this->authorize('update', $testRecord->batch);
            $testRecord->batch->update($batch);
        }

        if ($request->filled('do_update_items')) {
            $items = $request->get('items');
            $items = is_array($items) ? $items : json_decode($items);

            $testRecord->UpdateItems($items);
            $testRecord->load('items');  // 加载关系
        }

        if ($testRecord->update($request->all())) {
            return TestRecordResource::make($testRecord);
        }

        return $this->response()->failed('操作失败');
    }


    public function destroy($id)
    {
        $testRecord = TestRecord::where('id', $id)->firstOrFail();

        $this->authorize('delete', $testRecord);

        if ($testRecord->delete()) {
            $testRecord->items()->delete();

            event(new RecordDeleted($testRecord->batch));

            return $this->response()->noContent();
        }

        return $this->response()->failed('操作失败');
    }


    // 取样
    public function sample(ProductBatchRequest $request)
    {
        $this->authorize('create', TestRecord::class);

        $productName = $request->get('product_name');
        $productNameSuffix = $request->get('product_name_suffix');
        $batchNumber = $request->get('batch_number');
        $type = $request->get('type');

        // 校验
        $product = Product::where('internal_name', $productName)->firstOrFail();

        // step 1. 创建批次
        $batch = ProductBatch::where('product_name', $productName)
            ->where('product_name_suffix', $productNameSuffix)
            ->where('batch_number', $batchNumber)
            ->first();

        if (is_null($batch)) {
            $batch = ProductBatch::create([
                'product_name' => $productName,
                'product_name_suffix' => $productNameSuffix,
                'batch_number' => $batchNumber,
                'type' => $type,
            ]);
        }

        // step 2. 创建空白检测表单
        $testRecord = new TestRecord();

        $batch->testRecords()->save($testRecord);
        // 触发事件
        event(new QCSampled($batch, $testRecord));

        // step 3. 创建检测项目
        $test_way = $this->makeTestWay($product);

        $items = [];
        foreach ($test_way as $item) {
            if ($item['spec']['value_type'] == 'ONLY_SHOW') {

                // 混合粘度
                if ($item['name'] == "混合粘度") {
                    $item['spec']['data']['value'] = '|PASS';

                    if ($label_viscosity = (int)$product->label_viscosity) {
                        $viscosity_width = (int)$product->viscosity_width ?: 15;
                        if ($viscosity_width > 15) {
                            $viscosity_width = 15;
                        }

                        $viscosity = random_int($label_viscosity - $viscosity_width, $label_viscosity + $viscosity_width);

                        $item['spec']['data']['value'] = $product->label_viscosity . '±' . $product->viscosity_width . '|' . $viscosity;
                    }
                }

                $value = 'PASS';
                if (isset($item['spec']['data']['value']) && $item['spec']['data']['value']) {
                    $tmpArr = explode('|', $item['spec']['data']['value']);
                    $value = count($tmpArr) > 1 ? $tmpArr[1] : 'PASS';
                } elseif (isset($item['spec']['data']['max']) && $item['spec']['data']['max']) {
                    $value = $item['spec']['data']['max'];
                } elseif (isset($item['spec']['data']['min']) && $item['spec']['data']['min']) {
                    $value = $item['spec']['data']['min'];
                }

                // 未测试，但要展示的项目
                array_push($items, [
                    'item' => $item['name'],
                    'spec' => $item['spec'],
                    'value' => $value,
                    'conclusion' => 'PASS'
                ]);
            } else {
                array_push($items, [
                    'item' => $item['name'],
                    'spec' => $item['spec'],
                ]);
            }
        }

        // 更新粘度值
        $this->modifyNiandu($items);

        $testRecord->items()->createMany($items);

        // loading relationships
        $testRecord->load(['batch', 'items']);

        return TestRecordResource::make($testRecord);
    }

    public function archive(TestRecord $testRecord)
    {
        $this->authorize('update', $testRecord);

        if (empty($testRecord->testers)) {
            return $this->response()->failed('请输入检测员');
        }

        $allowEmptyItems = ['主剂', '固化剂', '配油'];

        foreach ($testRecord->items as $item) {
            if (in_array($item->item, $allowEmptyItems)) {
                continue;
            }

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

        if ($testRecord->conclusion == 'NG' && is_null($testRecord->willDispose)) {
            return $this->response()->failed('此批次不合格，并且还未提交处理意见，不能归档');
        }

        // 留空就表示合格, 这里帮忙填入PASS
        $testRecord->items()
            ->whereNull('conclusion')
            ->update(['conclusion' => 'PASS']);

        $testRecord->is_archived = true;

        if ($testRecord->save()) {
            return TestRecordResource::make($testRecord);
        }

        return $this->response()->failed('操作失败');
    }

    public function sayPackage(TestRecord $testRecord)
    {
        $this->authorize('update', $testRecord);

        if (empty($testRecord->testers)) {
            return $this->response()->failed('请输入检测员');
        }

        if ($testRecord->conclusion == 'NG' && is_null($testRecord->willDispose)) {
            return $this->response()->failed('此批次不合格，并且还未提交处理意见，不能写装');
        }

        $testRecord->said_package_at = now();

        if ($testRecord->save()) {
            return TestRecordResource::make($testRecord);
        }

        return $this->response()->failed('操作失败');
    }

    public function cancelArchived(TestRecord $testRecord)
    {
        // $this->authorize('update', $testRecord);

        $testRecord->is_archived = false;

        if ($testRecord->save()) {
            return TestRecordResource::make($testRecord);
        }

        return $this->response()->failed('操作失败');
    }

    public function cancelSayPackage(TestRecord $testRecord)
    {
        $this->authorize('update', $testRecord);

        $testRecord->said_package_at = null;

        if ($testRecord->save()) {
            return TestRecordResource::make($testRecord);
        }

        return $this->response()->failed('操作失败');
    }

    public function testDone(TestRecord $testRecord)
    {
        $this->authorize('update', $testRecord);

        $testRecord->completed_at = now();

        if ($testRecord->save()) {
            return TestRecordResource::make($testRecord);
        }

        return $this->response()->failed('操作失败');
    }

    protected function modifyNiandu(array &$items)
    {
        // 处理粘度
        foreach ($items as $key => $item) {
            if ($item['item'] == '60转粘度' || $item['item'] == '粘度' || $item['item'] == '6转粘度') {
                if ($item['item'] == '60转粘度') {
                    $items[$key]['value'] = \request('niandu60');
                } else {
                    $items[$key]['value'] = \request('niandu');
                }

                $items[$key]['conclusion'] = $this->makeConclusion($items[$key]);
            }
        }
    }

    protected function makeConclusion($item)
    {
        $value = $item['value'];
        // 对范围类型作结论
        if ($item['spec']['value_type'] == 'RANGE') {
            if (isset($item['spec']['data']['min']) && isset($item['spec']['data']['max'])) {
                if ($value > $item['spec']['data']['min'] && $value < $item['spec']['data']['max']) {
                    return 'PASS';
                }
            } elseif (isset($item['spec']['data']['min'])) {
                if ($value > $item['spec']['data']['min']) {
                    return 'PASS';
                }
            } elseif (isset($item['spec']['data']['max'])) {
                if ($value < $item['spec']['data']['max']) {
                    return 'PASS';
                }
            }

            return 'NG';
        }
    }

    protected function makeTestWay(Product $product): array
    {
        // 获取类别的检测要求
        $category = $product->category;
        $test_way = $category->testWay ? $category->testWay->way : [];

        // 合并产品的检测项目
        if ($product->testWay) {
            $test_way = $this->mergeTestWay($test_way, $product->testWay->way);
        }

        // 从产品获取粘度要求
        $spec_viscosity = $product->meta('spec_viscosity');
        if ($spec_viscosity) {
            $spec = explode("-", $spec_viscosity);
            if (count($spec) == 2) {
                list($min, $max) = $spec;

                $name = '粘度';
                if ($this->hasItem($test_way, '6转粘度')) {
                    $name = '6转粘度';
                }

                $way_viscosity = [
                    'name' => $name,
                    'method' => '',
                    'method_id' => '',
                    'spec' => [
                        'is_show' => true,
                        'required' => true,
                        'value_type' => 'RANGE',
                        'data' => [
                            'min' => floatval($min),
                            'max' => floatval($max),
                            'value' => '',
                        ],
                    ],
                ];

                $this->mergeTestWay($test_way, [$way_viscosity]);
            }
        }

        // 从产品获取注意事项
        $memo = $product->meta('memo');
        if ($memo) {
            $way_memo = [
                'name' => '注意事项',
                'method' => '',
                'method_id' => 0,
                'spec' => [
                    'is_show' => true,
                    'required' => false,
                    'value_type' => 'INFO',
                    'data' => [
                        'value' => $memo,
                    ],
                ]
            ];
            $this->mergeTestWay($test_way, [$way_memo]);
        }

        // 配油提示(只实现了感光阻焊油墨)
        $mixinTips = '';
        if (in_array($category->slug, ['H-8100', 'H-9100', 'H-8100 SP', 'H-9100 SP'])) {
            if (!$this->hasItem($test_way, '固化剂') && !$this->hasItem($test_way, '配油')) {
                $mixinTips = $product->part_b ? $product->part_b . '; ' . $product->ab_ratio : '见工单上固化剂与比例';
            }
        } elseif ($category->slug == 'H-8100B/H-9100B') {
            if (!$this->hasItem($test_way, '主剂') && !$this->hasItem($test_way, '配油')) {
                $mixinTips = $product->part_a ? $product->part_a . '; ' . $product->ab_ratio : '相应主剂与比例';
            }
        }

        if ($mixinTips) {
            array_unshift($test_way, [
                'name' => '配油',
                'method' => '',
                'method_id' => 0,
                'spec' => [
                    'is_show' => true,
                    'required' => false,
                    'value_type' => 'INFO',
                    'data' => [
                        'value' => $mixinTips,
                    ],
                ]
            ]);
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
