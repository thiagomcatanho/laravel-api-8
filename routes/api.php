<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\IncomeController;

use Illuminate\Http\Request;
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
// Customer Routes

Route::apiResource('customer', CustomerController::class);
Route::get('/customers/reporlist', [CustomerController::class, 'reportListing']);

// Income Routes
Route::apiResource('income', IncomeController::class);

