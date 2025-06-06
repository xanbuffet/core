<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;


/* == Auth routes == */
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/* == Menu routes == */
Route::get('/menus', [MenuController::class, 'index']);
Route::get('/menus/{day}', [MenuController::class, 'show']);

Route::middleware('auth:sanctum')->group(function() {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
});

Route::post('/webhook', WebhookController::class);
