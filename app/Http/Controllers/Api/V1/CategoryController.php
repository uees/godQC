<?php

namespace App\Http\Controllers\Api\V1;

use App\Category;
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

    public function show(Category $category)
    {
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

    public function destroy(Category $category)
    {
        $category->delete();

        return $this->noContent();
    }

    public function selectTestWay()
    {
        // todo
    }
}
