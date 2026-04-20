<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\Admin\AdminProductController;
use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminOrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;

// API Routes
// Route::middleware('auth:sanctum')->group(function () {
//     Route::apiResource('addresses', AddressController::class);
// });

// address routes (protected by auth:sanctum middleware)

    Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('addresses', AddressController::class);
});

// Cart routes (protected by auth:sanctum middleware)
Route::prefix('cart')->group(function () {
    // routes السلة
Route::get('/', [CartController::class, 'index']);
Route::post('/add', [CartController::class, 'add']);
Route::put('/{id}', [CartController::class, 'update']);
Route::delete('/{id}', [CartController::class, 'remove']);
Route::delete('/clear', [CartController::class, 'clear']);

});
// Admin routes (protected by auth:sanctum and admin middleware)
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/products', [AdminProductController::class,'index']);
    Route::post('/products', [AdminProductController::class,'store']);
    Route::put('/products/{id}', [AdminProductController::class,'update']);
    Route::post('/products/{id}/images', [ProductController::class, 'uploadImages']);
    Route::PUT('/products/{id}/{hidden}', [AdminProductController::class,'updateHiddenStatus']);
    Route::delete('/products/{id}', [AdminProductController::class,'destroy']);
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::get('/orders/latest', [AdminOrderController::class, 'latest']);
    Route::patch('/orders/{order}', [AdminOrderController::class, 'updateStatus']);
    Route::get('/orders/{order}', [AdminOrderController::class, 'show']);
});
// Category route
Route::get('/categories', [ProductController::class, 'GetCategories']);
Route::get('/category/{id}', [ProductController::class, 'getCategory']);
Route::get('/categories/{categoryId}/products', [ProductController::class, 'getCategoryProducts']);
Route::get('/categories-with-products', [ProductController::class, 'getCategoriesWithProducts']);

// Checkout route
// Route::post('/checkout', [CheckoutController::class,'checkout']);
// Product routes
Route::get('/products', [ProductController::class,'index']);
Route::get('/products/{id}', [ProductController::class,'show']);

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
    // Address routes (protected by auth:sanctum middleware)
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/addresses', [AddressController::class,'index']);
// });
    // My orders routes (protected by auth:sanctum middleware)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-orders', [OrderController::class,'myOrders']);
    Route::get('/my-orders/{id}', [OrderController::class,'show']);

});
// checkout route
Route::post('/checkout',[CheckoutController::class,'checkout'])
    ->middleware('auth:sanctum');
});