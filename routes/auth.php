<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DebtorController;
use Illuminate\Support\Facades\Route;

// ['user', 'password'] => returns $token
// TODO als er geen wachtwoord is maar wel een debtor_number dan terug sturen om aan te maken
Route::get('/authenticate', [AuthenticationController::class, 'authenticate']);

// TODO route aanmaken voor wachtwoord instellen

//TODO je ziet als eerst groep en daarin producten
// TODO opties voor voorraad bijhouden -> default uit

// ['email'] => returns $token
Route::get('/request-reset-password', [AuthenticationController::class, 'requestResetPassword']);

// ['password'] => returns $status
Route::post('/reset-password/{token}', [AuthenticationController::class, 'resetPassword']);

// ['debtor_number', 'name_1', 'name_2', 'search_name', 'address', 'postalcode', 'city', 'country', 'contact', 'phonenumber', 'mobile', 'email', 'email_cc', 'email_invoice', 'email_invoice_cc', 'tax_number', 'password']
Route::post('register', [AuthenticationController::class, 'register']);

// TODO - delete
// TODO account update
