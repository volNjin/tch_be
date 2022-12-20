<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:admin']], function () {
    Route::post('auth/login', [LoginController::class, 'login']);
});