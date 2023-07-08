<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DebtorController;
use Illuminate\Support\Facades\Route;

// ['user', 'password'] => returns $token
Route::post('/authenticate', [AuthenticationController::class, 'authenticate']);

// ['email'] => returns $token
Route::get('/request-reset-password', [AuthenticationController::class, 'requestResetPassword']);

// ['password'] => returns $status
Route::post('/reset-password/{token}', [AuthenticationController::class, 'resetPassword']);

// ['debtor_number', 'name_1', 'name_2', 'search_name', 'address', 'postalcode', 'city', 'country', 'contact', 'phonenumber', 'mobile', 'email', 'email_cc', 'email_invoice', 'email_invoice_cc', 'tax_number', 'password']
Route::post('register', [AuthenticationController::class, 'register']);
