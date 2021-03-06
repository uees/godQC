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

Route::post('auth/login', 'AuthController@login');
Route::post('auth/refresh', 'AuthController@refresh');

Route::get('suggests/templates', 'SuggestController@templates');

Route::middleware('jwt.auth')->group(function () {
    Route::get('auth/me', 'AuthController@me');
    Route::post('auth/logout', 'AuthController@logout');

    Route::post('categories/{category}/qc-ways', 'CategoryController@selectTestWay');
    Route::post('categories/{category}/templates', 'CategoryController@updateTemplates');

    Route::post('products/{product}/qc-ways', 'ProductController@selectTestWay');
    Route::post('products/{product}/templates', 'ProductController@updateTemplates');

    Route::post('product-disposes/{productDispose}/sample', 'ProductDisposeController@sample');

    Route::get('product-batches/disposes', 'ProductDisposeController@batchDispose');

    Route::post('customers/{customer}/products', 'CustomerController@selectProducts');
    Route::post('customers/{customer}/products/add', 'CustomerController@selectProduct');

    Route::post('qc-records/sample', 'QCRecordController@sample');
    Route::get('qc-records/{testRecord}/test-done', 'QCRecordController@testDone');
    Route::get('qc-records/{testRecord}/say-package', 'QCRecordController@sayPackage');
    Route::get('qc-records/{testRecord}/say-package/cancel', 'QCRecordController@cancelSayPackage');
    Route::get('qc-records/{testRecord}/archive', 'QCRecordController@archive');
    Route::get('qc-records/{testRecord}/archive/cancel', 'QCRecordController@cancelArchived');

    Route::post('mix-qc-records/sample', 'MixinTestRecordController@sample');
    Route::get('mix-qc-records/{testRecord}/test-done', 'MixinTestRecordController@testDone');
    Route::get('mix-qc-records/{testRecord}/archive', 'MixinTestRecordController@archive');
    Route::get('mix-qc-records/{testRecord}/archive/cancel', 'MixinTestRecordController@cancelArchived');

    Route::apiResources([
        'categories' => 'CategoryController',
        'customers' => 'CustomerController',
        'customer-requirements' => 'CustomerRequirementController',
        'product-batches' => 'ProductBatchController',
        'products' => 'ProductController',
        'product-disposes' => 'ProductDisposeController',
        'qc-methods' => 'QCMethodController',
        'qc-records' => 'QCRecordController',
        'mix-qc-records' => 'MixinTestRecordController',
        'qc-ways' => 'QCWayController',
        'pattern-tests/h-8100' => 'PatternTestController',
        'pattern-tests/a-9060' => 'A9060PatternTestController',
        'roles' => 'RoleController',
        'users' => 'UserController',
        'suggests' => 'SuggestController',
    ]);

    Route::prefix('qc-record-items')->name('record-items.')->group(function () {
        Route::get('{testRecord}', 'QCRecordItemController@index')->name('index');
        Route::post('{testRecord}', 'QCRecordItemController@store')->name('store');
        Route::get('{testRecord}/{testRecordItem}', 'QCRecordItemController@show')->name('show');
        Route::put('{testRecord}/{testRecordItem}', 'QCRecordItemController@update')->name('update');
        Route::patch('{testRecord}/{testRecordItem}', 'QCRecordItemController@update')->name('update');
        Route::delete('{testRecord}/{testRecordItem}', 'QCRecordItemController@destroy')->name('destroy');
    });

    Route::prefix('mix-qc-record-items')->name('mix-record-items.')->group(function () {
        Route::get('{testRecord}', 'MixinTestRecordItemController@index')->name('index');
        Route::post('{testRecord}', 'MixinTestRecordItemController@store')->name('store');
        Route::get('{testRecord}/{testRecordItem}', 'MixinTestRecordItemController@show')->name('show');
        Route::put('{testRecord}/{testRecordItem}', 'MixinTestRecordItemController@update')->name('update');
        Route::patch('{testRecord}/{testRecordItem}', 'MixinTestRecordItemController@update')->name('update');
        Route::delete('{testRecord}/{testRecordItem}', 'MixinTestRecordItemController@destroy')->name('destroy');
    });

    Route::prefix('statistics')->group(function () {
        Route::post('total/{year}/{month}/{type}', 'StatisticsController@makeTestStatistics');
        Route::post('failed/{year}/{month}/{type}', 'StatisticsController@makeDisqualificationStatistics');
        Route::get('failed/{year}/{month}/{type}', 'StatisticsController@showFailedAll');
        Route::get('shape-failed/{year}', 'StatisticsController@showFailedShape');
        Route::get('shape/{year}', 'StatisticsController@showStatisticsShape');
        Route::get('{year}/{month}/{type}', 'StatisticsController@showStatistics');
    });
});
