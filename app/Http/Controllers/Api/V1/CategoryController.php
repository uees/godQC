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
        $category = new Category();

        $category->fill($request->only(['name', 'slug', 'memo']))
            ->save();

        return CategoryResource::make($category);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->fill($request->only(['name', 'slug', 'memo']))
            ->save();

        return CategoryResource::make($category);
    }

    public function destroy($id)
    {
        if (Category::destroy($id)){
            return $this->noContent();
        }

        return $this->failed('操作失败');
    }

    public function selectTestWay(Category $category)
    {
        $testWayId = request('test_way_id');

        if (TestWay::where('id', $testWayId)->exists()) {
            $category->testWays()->sync([$testWayId]);

            return $this->noContent();
        }

        return $this->failed('操作失败');
    }
}
