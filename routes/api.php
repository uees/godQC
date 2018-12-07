<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->namespace('Api\V1')->group(function () {
    Route::post('auth/login', 'AuthController@login');
    Route::post('auth/refresh', 'AuthController@refresh');
    Route::post('auth/logout', 'AuthController@logout');
    Route::post('auth/logout-anywhere', 'AuthController@logoutAnywhere');

    Route::get('users/me', 'AuthController@me');
    Route::get('users/testers', 'UserController@testers');
    Route::post('categories/{category}/qc-ways', 'CategoryController@selectTestWay');
    Route::post('products/{product}/qc-ways', 'ProductController@selectTestWay');
    Route::post('customers/{customer}/products', 'CustomerController@selectProducts');
    Route::post('customers/{customer}/products/add', 'CustomerController@selectProduct');
    Route::post('qc-records/sample', 'QCRecordController@sample');
    Route::post('product-disposes/{productDispose}/sample', 'ProductDisposeController@sample');
    Route::patch('qc-records/{testRecord}/test-done', 'QCRecordController@testDone');
    Route::patch('qc-records/{testRecord}/say-package', 'QCRecordController@sayPackage');

    Route::apiResources([
        'categories' => 'CategoryController',
        'customers' => 'CustomerController',
        'customer-requirements' => 'CustomerRequirementController',
        'product-batchs' => 'ProductBatchController',
        'products' => 'ProductController',
        'product-disposes' => 'ProductDisposeController',
        'qc-methods' => 'QCMethodController',
        'qc-records' => 'QCRecordController',
        'qc-ways' => 'QCWayController',
        'roles' => 'RoleController',
        'users' => 'UserController',
    ]);

    Route::prefix('qc-record-items')->name('record-items.')->group(function () {
        Route::get('{testRecord}', 'QCRecordItemController@index')->name('index');
        Route::post('{testRecord}', 'QCRecordItemController@index')->name('store');
        Route::get('{testRecord}/{testRecordItem}', 'QCRecordItemController@show')->name('show');
        Route::put('{testRecord}/{testRecordItem}', 'QCRecordItemController@update')->name('update');
        Route::patch('{testRecord}/{testRecordItem}', 'QCRecordItemController@update')->name('update');
        Route::delete('{testRecord}/{testRecordItem}', 'QCRecordItemController@destroy')->name('destroy');
    });

});
