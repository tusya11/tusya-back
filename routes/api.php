<?php

use App\Http\Controllers\AuthController;
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
    Route::get('products', [ProductsController::class, 'getAll']);
    Route::get('products/{id}', [ProductsController::class, 'getById']);
    Route::post('products/', [ProductsController::class, 'create'])->middleware(['checkRole:Администратор']);
    Route::post('products/{id}', [ProductsController::class, 'edit'])->middleware(['checkRole:Администратор']);

    Route::post('cart/add', [ProductsController::class, 'addToCart']);
    Route::post('cart/favourite', [ProductsController::class, 'addToFavourite']);

    Route::post('profile/edit', [UserController::class, 'editProfile']);

});