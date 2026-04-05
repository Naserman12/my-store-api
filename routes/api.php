<?php

use App\Http\Controllers\API\AddressController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;

// API Routes

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('addresses', AddressController::class);

});

// address routes (protected by auth:sanctum middleware)
Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('addresses', AddressController::class);

});

// Cart routes (protected by auth:sanctum middleware)
Route::prefix('cart')->group(function () {

    Route::get('/', [CartController::class,'index']);
    Route::post('/add', [CartController::class,'add']);
    Route::put('/item/{id}', [CartController::class,'update']);
    Route::delete('/item/{id}', [CartController::class,'remove']);
    Route::delete('/clear', [CartController::class,'clear']);

});
// Category route
Route::get('categories', [ProductController::class, 'GetCategories']);
// Checkout route
Route::post('/checkout', [CheckoutController::class,'checkout']);
// Product routes
Route::get('/products', [ProductController::class,'index']);
Route::get('/products/{slug}', [ProductController::class,'show']);

// Profile routes (protected by auth:sanctum middleware)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class,'show']);
    Route::put('/profile', [ProfileController::class,'update']);
    Route::put('/profile/password', [ProfileController::class,'updatePassword']);
    Route::post('/profile/avatar', [ProfileController::class,'updateAvatar']);

});

// ?Auth routes
Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class,'register']);
    Route::post('/login', [AuthController::class,'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class,'logout']);
        Route::get('/user', [AuthController::class,'user']);
    });
    // My orders routes (protected by auth:sanctum middleware)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-orders', [OrderController::class,'myOrders']);
    Route::get('/my-orders/{id}', [OrderController::class,'show']);

});
// checkout route
Route::post('/checkout',[OrderController::class,'checkout'])
    ->middleware('auth:sanctum');
});