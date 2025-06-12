<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::resource('menus', MenuController::class)->only(['index', 'show']);

Route::post('tracking', [OrderController::class, 'tracking']);
Route::apiResource('orders', OrderController::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('users/{user}/password_change', [UserController::class, 'updatePassword']);
    Route::apiResource('users', UserController::class);
});

Route::post('/webhook', WebhookController::class);
