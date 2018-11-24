<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::get();

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {

    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {

    }

    public function destroy(Category $category)
    {

    }
}
