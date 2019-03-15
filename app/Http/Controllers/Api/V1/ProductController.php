<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Controllers\Controller;
use App\Product;
use App\TestWay;

class ProductController extends Controller
{
    // sort_by,order,with,q,category_id
    public function index()
    {
        $perPage = $this->perPage();
        $query = Product::query();

        $this->loadRelByQuery($query);

        $this->parseWhere($query, ['category_id']);

        if ($search = \request('q')) {
            $condition = queryCondition('internal_name', $search);

            $query->where($condition);
        }

        $pagination = $query->orderBy($this->sortBy(), $this->order())
            ->paginate($perPage)
            ->appends(request()->except('page'));

        return ProductResource::collection($pagination);
    }


    public function store(ProductRequest $request)
    {
        $this->authorize('create', Product::class);

        $metas = $request->get('metas');
        // 格式化数据
        if (!is_null($metas)) {
            $metas = is_array($metas) ? $metas : json_decode($metas);
        }

        $product = new Product();

        $product->fill($request->except('metas'));

        $product->metas = $metas;

        $product->save();

        // 加载关系
        $this->loadRelByModel($product);

        return ProductResource::make($product);
    }


    public function show($id)
    {
        $query = Product::query();

        $this->loadRelByQuery($query);

        $product = $query->findOrFail($id);

        return ProductResource::make($product);
    }


    public function update(ProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        $this->authorize('update', $product);

        $metas = $request->get('metas');
        // 格式化数据
        if (!is_null($metas)) {
            $metas = is_array($metas) ? $metas : json_decode($metas);
        }

        $product->fill($request->except('metas'));

        $product->metas = $metas;

        $product->save();

        // 加载关系
        $this->loadRelByModel($product);

        return ProductResource::make($product);
    }


    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $this->authorize('delete', $product);

        if ($product->delete()) {
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }

    public function selectTestWay(Product $product)
    {
        $this->authorize('updateTestway', $product);

        $testWayId = request('test_way_id');

        if (empty($testWayId) || (int)$testWayId == 0) {
            $testWay = $product->testWay;

            $product->testWay()->dissociate();
            $product->save();

            if (!$testWay->products()->exists() && !$testWay->categories()->exists()) {
                if (request()->user()->can('delete', $testWay)) {
                    $testWay->delete();
                }
            }

            return $this->noContent();
        }

        if (TestWay::where('id', $testWayId)->exists()) {
            $product->testWay()->associate($testWayId);
            $product->save();

            return $this->noContent();
        }

        return $this->failed('操作失败');
    }

    public function updateTemplates(Product $product)
    {
        $this->authorize('updateTemplates', $product);

        $templates = request('templates');
        $is_cancel_category_template = (boolean)request('cancel_category_template');

        if (is_null($templates)) {
            // clear templates
            $product->setMeta('templates', null);
            $product->setMeta('cancel_category_template', false);
        } else {
            $templates = is_array($templates) ? $templates : json_decode($templates);
            $product->setMeta('templates', $templates);
            $product->setMeta('cancel_category_template', $is_cancel_category_template);
        }

        $product->save();

        return $this->noContent();
    }
}
