<?php

use App\Http\Controllers\ApiControllers\AuthController;
use App\Http\Controllers\ApiControllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'v1'], function () {
    Route::namespace('ApiControllers')->group(function () {
        Route::group(['prefix' => 'auth'], function () {
            Route::post('signup', [AuthController::class, 'signup']);
            Route::post('signin', [AuthController::class, 'signin']);
        });
        Route::group(['middleware' => 'auth:api'], function () {
            Route::group(['prefix' => 'profile'], function () {
                Route::get('show', [UserController::class, 'show']);
                Route::put('update', [UserController::class, 'update']);
                Route::put('change_password', [UserController::class, 'changePassword']);
                Route::delete('delete', [UserController::class, 'delete']);
            });
        });
    });
});
