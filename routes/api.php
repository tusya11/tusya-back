<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UserController;
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

Route::prefix('auth')->group(function () {
    Route::post('registration', [AuthController::class, 'registration']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::group(['middleware' => 'auth:api'], function () {

    // Products
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductsController::class, 'getAll'])->withoutMiddleware(['auth:api']);
        Route::get('/{id}', [ProductsController::class, 'getById'])->withoutMiddleware(['auth:api']);
        Route::post('/', [ProductsController::class, 'create'])->middleware(['checkRole:Администратор']);
        Route::post('/{id}', [ProductsController::class, 'edit'])->middleware(['checkRole:Администратор']);
        Route::delete('/{id}', [ProductsController::class, 'delete'])->middleware(['checkRole:Администратор']);
    });

    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'getMyProducts']);
        Route::get('/favorite', [CartController::class, 'getMyProductsFavorite']);
        Route::post('/add', [CartController::class, 'addToCart']);
        Route::post('/favorite', [CartController::class, 'addToFavourite']);
    });

    // Profile
    Route::prefix('profile')->group(function () {
        Route::post('/edit', [UserController::class, 'editProfile']);
        Route::get('/', [UserController::class, 'getProfile']);
        Route::get('/{id}', [UserController::class, 'getProfileById'])->withoutMiddleware(['auth:api']);
    });

    // Categories
    Route::prefix('category')->group(function () {
        Route::post('/', [CategoryController::class, 'create']);
        Route::post('/{id}', [CategoryController::class, 'edit']);
        Route::get('/', [CategoryController::class, 'index'])->withoutMiddleware(['auth:api']);
        Route::get('/{id}', [CategoryController::class, 'getById'])->withoutMiddleware(['auth:api']);
    });

});
