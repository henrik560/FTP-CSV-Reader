<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Support\Facades\Route;

Route::post('/authenticate', [AuthenticationController::class, 'authenticate']);

Route::post('/reset-password', [AuthenticationController::class, 'resetPassword']);
Route::post('/request-reset', [AuthenticationController::class, 'requestResetPassword']);

// TODO add routing for authenication in routes/auth.php
// TODO - register
// TODO - forgot password
// TODO - authenticate
// TODO - delete account