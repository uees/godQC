<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\QCSampled;
use App\Events\RecordDeleted;
use App\Http\Controllers\Controller;
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

        if (\request()->filled('with')) {
            $query = $query->with(explode(',', request('with')));
        }

        $query = $this->parseFields($query);
        $query = $this->parseWhere($query, ['conclusion', 'show_reality', 'test_times', 'created_at']);

        if (request('said_package') == '1') {
            $query = $query->whereNotNull('said_package_at');
        } elseif (request('said_package') == '0') {
            $query = $query->whereNull('said_package_at');
        }

        if (\request()->filled('testing')) {
            $query = $query->where('is_archived', 0);
        } elseif (\request()->filled('tested')) {
            $query = $query->where('is_archived', 1);
        }

        if (\request()->filled('type')) {
            $query = $query->whereHas('batch', function (Builder $query) {
                $query->where('type', \request('type'));
            });
        }

        // 类别筛选支持
        if (\request('category')) {
            if ($category = Category::where('name', \request('category'))->first()) {
                $query = $query->whereHas('batch', function (Builder $query) use ($category) {
                    $products = $category->products->pluck('internal_name');
                    $query->whereIn('product_name', $products);
                });
            }
        }

        if (\request()->filled('q')) {
            $memo_condition = queryCondition('memo', \request('q'));
            $query = $query->where($memo_condition)
                ->orWhereHas('batch', function (Builder $query) {
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
        $testRecord = TestRecord::with('batch')
            ->where('id', $id)
            ->firstOrFail();

        $this->authorize('update', $testRecord);

        if ($request->filled('batch')) {
            $batch = $request->get('batch');
            $batch = is_array($batch) ? $batch : json_decode($batch);

            $this->authorize('update', $testRecord->batch);
            $testRecord->batch->update($batch);
        }

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

            $testRecord->items;  // 加载关系
        }

        if ($testRecord->update($request->all())) {
            return TestRecordResource::make($testRecord);
        }

        return $this->failed('操作失败');
    }


    public function destroy($id)
    {
        $testRecord = TestRecord::where('id', $id)->firstOrFail();

        $this->authorize('delete', $testRecord);

        if ($testRecord->delete()) {
            $testRecord->items()->delete();

            event(new RecordDeleted($testRecord->batch));

            return $this->noContent();
        }

        return $this->failed('操作失败');
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
        $testRecord = (new TestRecord());

        $batch->testRecords()->save($testRecord);

        // step 3. 创建检测项目
        $test_way = $this->makeTestWay($product);

        $items = [];
        foreach ($test_way as $item) {
            if ($item['spec']['value_type'] == 'ONLY_SHOW') {

                if (isset($item['spec']['data']['value']) && $item['spec']['data']['value']) {
                    $value = $item['spec']['data']['value'];
                } elseif (isset($item['spec']['data']['min']) && $item['spec']['data']['min']) {
                    $value = $item['spec']['data']['min'];
                } elseif (isset($item['spec']['data']['max']) && $item['spec']['data']['max']) {
                    $value = $item['spec']['data']['max'];
                } else {
                    $value = 'PASS';
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
        $this->appendNiandu($items);

        $testRecord->items()->createMany($items);


        // loading relationships
        $testRecord->batch;
        $testRecord->items;

        // 触发事件
        event(new QCSampled($batch, $testRecord));

        return TestRecordResource::make($testRecord);
    }

    public function archive(TestRecord $testRecord)
    {
        $this->authorize('update', $testRecord);

        /*
        $notDone = $testRecord->items()
            ->where(function ($query) {
                $query->whereNull('value')->orWhere('value', '');
            })
            ->where(function ($query) {
                $query->whereNull('conclusion')->orWhere('conclusion', '');
            })
            ->exists();

        if ($notDone) {
            return $this->failed('检测未完成， 不能归档');
        }
        */

        $allowEmptyItems = ['主剂', '固化剂', '配油'];

        foreach ($testRecord->items as $item) {
            if (in_array($item->item, $allowEmptyItems)) {
                continue;
            }

            if (isset($item->spec['required']) && $item->spec['required'] == false) {
                continue;
            }

            if (is_null($item->value) || $item->value == '') {
                return $this->failed("检测项目 '{$item['item']}' 的值未填写， 不能归档");
            }
        }

        if (empty($testRecord->conclusion)) {
            $testRecord->conclusion = 'PASS';
        }

        if ($testRecord->conclusion == 'NG' && is_null($testRecord->willDispose)) {
            return $this->failed('此批次不合格，并且还未提交处理意见，不能归档');
        }

        // 留空就表示合格, 这里帮忙填入PASS
        $testRecord->items()
            ->whereNull('conclusion')
            ->update(['conclusion' => 'PASS']);

        $testRecord->is_archived = true;

        if ($testRecord->save()) {
            return TestRecordResource::make($testRecord);
        }

        return $this->failed('操作失败');
    }

    public function sayPackage(TestRecord $testRecord)
    {
        $this->authorize('update', $testRecord);

        if (empty($testRecord->testers)) {
            return $this->failed('请输入检测员');
        }

        if ($testRecord->conclusion == 'NG' && is_null($testRecord->willDispose)) {
            return $this->failed('此批次不合格，并且还未提交处理意见，不能写装');
        }

        $testRecord->said_package_at = now();

        if ($testRecord->save()) {
            return TestRecordResource::make($testRecord);
        }

        return $this->failed('操作失败');
    }

    public function cancelArchived(TestRecord $testRecord)
    {
        // $this->authorize('update', $testRecord);

        $testRecord->is_archived = false;

        if ($testRecord->save()) {
            return TestRecordResource::make($testRecord);
        }

        return $this->failed('操作失败');
    }

    public function cancelSayPackage(TestRecord $testRecord)
    {
        $this->authorize('update', $testRecord);

        $testRecord->said_package_at = null;

        if ($testRecord->save()) {
            return TestRecordResource::make($testRecord);
        }

        return $this->failed('操作失败');
    }

    public function testDone(TestRecord $testRecord)
    {
        $this->authorize('update', $testRecord);

        $testRecord->completed_at = now();

        if ($testRecord->save()) {
            return TestRecordResource::make($testRecord);
        }

        return $this->failed('操作失败');
    }

    protected function appendNiandu(array &$items)
    {
        // 处理粘度
        foreach ($items as $key => $item) {
            if ($item['item'] == '粘度') {
                $items[$key]['value'] = \request('niandu');
            } elseif ($item['item'] == '6转粘度') {
                $items[$key]['value'] = \request('niandu');
            } elseif ($item['item'] == '60转粘度') {
                $items[$key]['value'] = \request('niandu60');
            }
        }
    }

    protected function makeTestWay(Product $product)
    {
        $category = $product->category;
        $test_way = $category->testWay ? $category->testWay->way : [];

        if ($productTestWay = $product->testWay) {
            $test_way = $this->mergeTestWay($test_way, $productTestWay->way);
        }

        if ($category->slug == 'H-8100' || $category->slug == 'H-9100') {
            if (!$this->hasItem($test_way, '固化剂')) {
                $value = $product->part_b
                    ? $product->part_b . '; ' . $product->ratio
                    : 'HD2; 3:1';
                array_unshift($test_way, [
                    'name' => '固化剂',
                    'method' => '',
                    'method_id' => 0,
                    'spec' => [
                        'is_show' => true,
                        'required' => false,
                        'value_type' => 'INFO',
                        'data' => [
                            'value' => $value,
                        ],
                    ]
                ]);
            }
        } elseif ($category->slug == 'H-8100B/H-9100B') {
            if (!$this->hasItem($test_way, '主剂')) {
                $value = $product->part_a
                    ? $product->part_a . '; ' . $product->ratio
                    : '8G; 3:1';
                array_unshift($test_way, [
                    'name' => '主剂',
                    'method' => '',
                    'method_id' => 0,
                    'spec' => [
                        'is_show' => true,
                        'required' => false,
                        'value_type' => 'INFO',
                        'data' => [
                            'value' => $value,
                        ],
                    ]
                ]);
            }
        } elseif ($category->slug == 'SPXX') {
            if (!$this->hasItem($test_way, '固化剂')) {
                $value = $product->part_b
                    ? $product->part_b . '; ' . $product->ratio
                    : 'HD12; 3:1';
                array_unshift($test_way, [
                    'name' => '固化剂',
                    'method' => '',
                    'method_id' => 0,
                    'spec' => [
                        'is_show' => true,
                        'required' => false,
                        'value_type' => 'INFO',
                        'data' => [
                            'value' => $value,
                        ],
                    ]
                ]);
            }
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
