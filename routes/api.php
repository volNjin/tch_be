<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\Auth\AuthController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\CategoryController;

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
Route::group(['prefix' => 'admin'], function(){
    Route::post('auth/login', [LoginController::class, 'login']);
    Route::post('category/create', [CategoryController::class, 'create']);
    Route::get('category/index', [CategoryController::class, 'index']);
    Route::get('category/update', [CategoryController::class, 'update']);
});


Route::post('user/auth/register', [AuthController::class, 'register']);
Route::post('user/auth/login', [AuthController::class, 'login']);
Route::post('user/auth/logout', [AuthController::class, 'logout']);
Route::post('user/auth/changePassword', [AuthController::class, 'changePassword']);
