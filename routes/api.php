<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Frontend\FrontendController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Authorization\RoleController;
use App\Http\Controllers\Api\Authorization\PermissionController;
use App\Http\Controllers\Api\Auth\SocialAuthController;


// Authentication Routes
Route::post('/register', RegisterController::class)->name('register');
Route::post('/login', LoginController::class)->name('login');
Route::post('/logout', LogoutController::class)->name('logout');

// Google Auth Routes
Route::get('google/redirect', [SocialAuthController::class, 'redirectToGoogle']);
Route::get('google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
Route::post('google/token', [SocialAuthController::class, 'loginWithGoogleToken']);

// Frontend Controller
Route::get('/getproduct', [FrontendController::class, 'product'])->name('index.getproduct');
Route::get('/getcategory', [FrontendController::class, 'category'])->name('index.getcategory');
Route::get('/detailproduct/{slug}', [FrontendController::class, 'productBySlug'])->name('index.detailproduct'); 

// Order Controller
Route::post('/order', [OrderController::class, 'store'])->name('order.store');
Route::get('/orders', [OrderController::class, 'showOrders'])->name('show.orders');
Route::get('/orders/today/count', [OrderController::class, 'showLengthOrderToday'])->name('count.orders.today');

// Admin Page
Route::middleware('auth:api')->group(function () {    
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/category', CategoryController::class);        
    Route::apiResource('/product', ProductController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permissions', PermissionController::class);
});
