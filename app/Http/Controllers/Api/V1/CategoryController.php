<?php

namespace App\Http\Controllers\Api\V1;

use App\Category;
use App\TestWay;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $query = Category::query();

        if (\request()->filled('q')) {
            $name_condition = queryCondition('name', \request('q'));
            $slug_condition = queryCondition('slug', \request('q'));

            $query = $query->where($name_condition)
                ->orWhere($slug_condition);
        }

        $categories = $query->get(); // 直接获取所有分类

        return CategoryResource::collection($categories);
    }

    public function show($id)
    {
        $query = Category::query();

        if (\request()->filled('with')) {
            $query = $query->with(explode(',', request('with')));
        }

        $category = $query->findOrFail($id);

        return CategoryResource::make($category);
    }

    public function store(CategoryRequest $request)
    {
        $this->authorize('create', Category::class);

        $category = new Category();

        $category->fill($request->only(['name', 'slug', 'memo']))
            ->save();

        return CategoryResource::make($category);
    }

    public function update(CategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);

        $this->authorize('update', $category);

        $category->fill($request->only(['name', 'slug', 'memo']))
            ->save();

        return CategoryResource::make($category);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $this->authorize('delete', $category);

        if ($category->delete()) {
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }

    public function selectTestWay(Category $category)
    {
        $this->authorize('update', $category);

        $testWayId = request('test_way_id');

        if (is_null($testWayId)) {
            $category->testWays()->delete();
            return $this->noContent();
        }

        if ($testWayId && TestWay::where('id', $testWayId)->exists()) {
            $category->testWays()->sync([$testWayId]);
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }
}
