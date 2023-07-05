<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Support\Facades\Route;

// ["user", "password"] => returns $token
Route::post('/authenticate', [AuthenticationController::class, 'authenticate']);

// ["password", "token"] => returns $status
Route::post('/reset-password/{token}', [AuthenticationController::class, 'resetPassword']);

// ["email"] => returns $token
Route::post('/request-reset', [AuthenticationController::class, 'requestResetPassword']);

Route::post('register', [AuthenticationController::class, 'register']);

// TODO - delete account