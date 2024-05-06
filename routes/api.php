<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FoodController;
// use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
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

// Routes for AuthController
Route::prefix('/')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('registeradmin', [AuthController::class, 'registeradmin']);
    Route::post('logout', [AuthController::class, 'logout']);
});
// Routes for AdminController without 'api/' prefix
// Route::prefix('admin/auth')->group(function () {
//     Route::post('login', [AdminController::class, 'login']);
//     Route::post('register', [AdminController::class, 'register']);
//     Route::post('logout', [AdminController::class, 'logout']);
// });
Route::prefix('food')->group(function () {
    Route::get('{search?}', [FoodController::class, 'index']);
    Route::post('/add', [FoodController::class, 'addMenu']);
    Route::put('/edit/{id_food}', [FoodController::class, 'updateMenu']);
    Route::middleware('auth:api')->delete('/delete/{id_food}', [FoodController::class, 'deleteMenu']);
});

Route::get('/order-details/{orderId}', [OrderController::class, 'showOrderDetails'])->name('order.details');

Route::post('/order', [OrderController::class, 'order'])->name('order.create');

Route::get('/order-list', [OrderController::class, 'getOrderDetails'])->name('order.list');

Route::middleware('auth:api')->get('/order', [OrderController::class, 'orderlist']);
