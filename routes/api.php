<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/* == Auth routes == */
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/* == Menu routes == */
Route::resource('menus', MenuController::class)->only(['index', 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::resource('users', UserController::class)->only([
        'index', 'show', 'update', 'destroy',
    ]);

    Route::resource('orders', OrderController::class)->only([
        'show', 'store', 'update', 'destroy',
    ]);
});

Route::post('/webhook', WebhookController::class);
