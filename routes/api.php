<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;

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

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes that require authentication
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::get('/user', [UserController::class, 'getUser']);
    Route::get('/user/cart', [UserController::class, 'getUserCart']);
    Route::get('/user/purchase-history', [UserController::class, 'getUserPurchaseHistory']);
    // Add more routes for user-related functionality as needed

    // Product routes
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    // Add more routes for product-related functionality as needed
});


/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */