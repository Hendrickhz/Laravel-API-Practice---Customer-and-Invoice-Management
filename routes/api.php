<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\InvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//api v1
Route::prefix('v1')->namespace('App\Http\Controllers\Api\V1')->group(function () {

    // Middleware group for authenticated routes using Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        // Route for bulk storing invoices
        Route::post('invoices/bulkStore', [InvoiceController::class, 'bulkStore'])->name('invoices.bulkStore');

        // Resource routes for customers (CRUD operations)
        Route::apiResource('customers', CustomerController::class);

        // Resource routes for invoices (CRUD operations)
        Route::apiResource('invoices', InvoiceController::class);
    });

    // Subgroup for user-related authentication routes
    Route::prefix('user')->controller(AuthController::class)->group(function () {
        // Route for user registration
        Route::post('register', 'register')->name('register');

        // Route for user login
        Route::post('login', 'login')->name('login');

        // Middleware group for authenticated user routes using Sanctum
        Route::middleware('auth:sanctum')->group(function () {
            // Route for user logout
            Route::post('logout', 'logout')->name('logout');

            // Route for logging out the user from all devices
            Route::post('logout-all', 'logoutAll')->name('logoutAll');

            // Route for retrieving authenticated user's devices
            Route::get('devices', 'devices')->name('devices');
        });
    });
});

//
