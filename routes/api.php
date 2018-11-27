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
        'users' => 'UserController',
    ]);
});
