<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/reset-password', [AuthenticationController::class, 'resetPassword']);
Route::get('/token', [AuthenticationController::class, 'token']);


Route::get('/', function () {
    return view('welcome');
});

require __DIR__ . '/auth.php';
