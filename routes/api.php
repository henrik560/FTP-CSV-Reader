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
    Route::get('/products', [ProductsController::class, 'index']);

    Route::prefix('/debtors')->group(function () {
        Route::get('/', [DebtorController::class, 'index']);
        Route::get('/{debtorId}', [DebtorController::class, 'show']);
        Route::get('/{debtor}/products', [DebtorController::class, 'products']);
    });
});

// TODO move to routes/auth.php
Route::post('/authenticate', [AuthenticationController::class, 'index']);

// TODO add routing for authenication in routes/auth.php
// TODO - register
// TODO - forgot password
// TODO - authenticate
// TODO - delete account
