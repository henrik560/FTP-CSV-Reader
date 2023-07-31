<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DebtorController;
use App\Http\Controllers\ProductsController;
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

Route::middleware('api.key.validation')->group(function () {
    Route::prefix("/products")->group(function () {
        Route::prefix('/groups')->group(function () {
            Route::get('/', [ProductsController::class, 'index']);
            Route::get('/{group}', [ProductsController::class, 'show']);
        });
    });

    Route::prefix('/debtors')->group(function () {
        Route::get('/', [DebtorController::class, 'index']);
        Route::prefix('/{debtorId}')->group(function () {
            Route::get('/', [DebtorController::class, 'show']);
            Route::get('/products', [DebtorController::class, 'products']);
            Route::get('/delete', [DebtorController::class, 'delete']);
        });
    });
});

// USER Authentication
/**
 * 
 * ! - POST  /user/login (username && password) -> [ $user_info, $token, $status ]; generates a 24h token, after 24h login again
 * ! - POST  /user/logout (username && token) -> [ $status ]; token is removed
 * ! - POST  /user (userdata) -> [ $user_info, $token, $status ]
 * ! - PATCH /user/{id}/update (userdata) -> [ $status ]
 * ! - GET   /request-reset-password -> [ Mailable -> $reset-token ]
 * ! - POST  /reset-password/{token} -> [ $status ];
 */
Route::prefix('/user')->group(function () {
    Route::post('/login', [AuthenticationController::class, 'login']);
    Route::post('/logout', [AuthenticationController::class, 'logout']);

    Route::prefix('/{userId}')->group(function () {
        Route::post('/request-reset-password', [AuthenticationController::class, 'requestResetPassword']);
        Route::post('/reset-password/{token}', [AuthenticationController::class, 'resetPassword']);
    });
});

Route::resource('/users', DebtorController::class);
