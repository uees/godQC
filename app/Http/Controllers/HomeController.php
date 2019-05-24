<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return redirect('/erp.html');
    }

    // 更新 metas 数据，支持模板和粘度
    public function update_2019_05_24()
    {
        foreach (Category::get() as $category) {
            $metas = $category->metas;
            if (is_null($metas)) {
                $metas = [];
            }
            if (is_object($metas)) {
                $metas = object2array($metas);
            }
            $metas = array_merge(['templates' => []], $metas);
            $category->metas = $metas;
            $category->save();
        }

        foreach (Product::get() as $product) {
            $metas = $product->metas;
            if (is_null($metas)) {
                $metas = [];
            }
            if (is_object($metas)) {
                $metas = object2array($metas);
            }
            $metas = array_merge([
                'templates' => [],
                'cancel_category_template' => false,
                'spec_viscosity' => '',
            ], $metas);
            $product->metas = $metas;
            $product->save();
        }

        return 'update done';
    }
}
