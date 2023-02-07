<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\User\Auth\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ToppingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\User\AddressNoteController;
use App\Http\Controllers\User\UpdateInfo;

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

    Route::post('category/create', [CategoryController::class, 'create']);
    Route::get('category/index', [CategoryController::class, 'index']);
    Route::post('category/update', [CategoryController::class, 'update']);
    Route::post('category/indexByParentId', [CategoryController::class, 'indexByParentId']);

    Route::post('product/create', [ProductController::class, 'create']);
    Route::get('product/index', [ProductController::class, 'index']);
    Route::post('product/update', [ProductController::class, 'update']);
    Route::post('product/indexByCategoryId', [ProductController::class, 'indexByCategoryId']);
    Route::post('product/getProductInfo',[ProductController::class, 'getProductInfo']);
    
    Route::post('product/getToppingInfo',[ToppingController::class, 'getToppingInfo']);

    Route::post('order/addOrder', [OrderController::class, 'addOrder']);
    Route::post('order/getOrders', [OrderController::class, 'getOrders']);
    Route::get('order/getUnsuccessOrders', [OrderController::class, 'getUnsuccessOrders']);
    Route::get('order/getSuccessOrders', [OrderController::class, 'getSuccessOrders']);
    Route::post('order/getOrderItems', [OrderController::class, 'getOrderItems']);
    Route::post('order/acceptOrder', [OrderController::class, 'acceptOrder']);
    Route::post('order/cancelOrder', [OrderController::class, 'cancelOrder']);
});

route::group(['prefix' => 'user'], function(){
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/checkOtp', [AuthController::class, 'checkOtp']);
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::post('info/updateInfo', [UpdateInfo::class, 'update']);

    Route::post('info/getAddressNote', [AddressNoteController::class, 'getAddressNote']);
});
route::group(['prefix' => 'payment'], function(){
    Route::post('vnpay', [PaymentController::class, 'vnpay_payment']);
    Route::post('momo', [PaymentController::class, 'momo_payment']);
    Route::post('zalopay', [PaymentController::class, 'zalopay_payment']);
});