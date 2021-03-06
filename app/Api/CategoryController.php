<?php

namespace App\Api;

use App\Category;
use App\TestWay;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $query = Category::query();

        $this->loadRelByQuery($query);

        if ($search = \request('q')) {
            $query->where(function ($query) use ($search) {
                $name_condition = queryCondition('name', $search);
                $slug_condition = queryCondition('slug', $search);

                $query->where($name_condition)
                    ->orWhere($slug_condition);
            });
        }

        $categories = $query->get(); // 直接获取所有分类

        return CategoryResource::collection($categories);
    }

    public function show($id)
    {
        $query = Category::query();

        $this->loadRelByQuery($query);

        $category = $query->findOrFail($id);

        return CategoryResource::make($category);
    }

    public function store(CategoryRequest $request)
    {
        $this->authorize('create', Category::class);

        $metas = $request->get('metas');
        if (!is_null($metas)) {
            $metas = is_array($metas) ? $metas : json_decode($metas, true);
        }

        if (!is_array($metas)){
            $metas = [];
        }

        $metas = array_merge(['templates' => []], $metas);

        $category = new Category();
        $category->fill($request->only(['name', 'slug', 'memo']));

        $category->metas = $metas;

        $category->save();

        return CategoryResource::make($category);
    }

    public function update(CategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);

        $this->authorize('update', $category);

        $metas = $request->get('metas');
        if (!is_null($metas)) {
            $metas = is_array($metas) ? $metas : json_decode($metas, true);
        }

        if (!is_array($metas)){
            $metas = [];
        }

        $metas = array_merge(['templates' => []], $metas);

        $category->fill($request->only(['name', 'slug', 'memo']));

        $category->metas = $metas;

        $category->save();

        $this->loadRelByModel($category);

        return CategoryResource::make($category);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $this->authorize('delete', $category);

        if ($category->delete()) {
            return $this->response()->noContent();
        }

        return $this->response()->failed('操作失败');
    }

    public function selectTestWay(Category $category)
    {
        $this->authorize('updateTestway', $category);

        $testWayId = request('test_way_id');

        // clear way
        if (empty($testWayId) || (int)$testWayId == 0) {
            $testWay = $category->testWay;

            $category->testWay()->dissociate();  // 此方法会设置关联外键为 null
            $category->save();

            // 没有使用的 testWay 直接删除
            if (!$testWay->products()->exists() && !$testWay->categories()->exists()) {
                if (request()->user()->can('delete', $testWay)) {
                    $testWay->delete();
                }
            }

            return $this->response()->noContent();
        }

        // update way
        if (TestWay::where('id', $testWayId)->exists()) {
            $category->testWay()->associate($testWayId);
            $category->save();

            return $this->response()->noContent();
        }

        return $this->response()->failed('操作失败');
    }

    public function updateTemplates(Category $category)
    {
        $this->authorize('updateTemplates', $category);

        $templates = request('templates');

        if (is_null($templates)) {
            // clear templates
            $category->setMeta('templates', []);
        } else {
            // json-editor 会把数据转为 json 字符串
            $templates = is_array($templates) ? $templates : json_decode($templates);
            $category->setMeta('templates', $templates);
        }

        $category->save();

        return $this->response()->noContent();
    }
}
