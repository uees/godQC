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

    Route::post('categories/{category}/qc-ways', 'CategoryController@selectTestWay');
    Route::post('products/{product}/qc-ways', 'ProductController@selectTestWay');
    Route::post('customers/{customer}/products', 'CustomerController@selectProducts');
    Route::post('customers/{customer}/products/add', 'CustomerController@selectProduct');
    Route::post('qc-records/sample', 'QCRecordController@sample');
});
