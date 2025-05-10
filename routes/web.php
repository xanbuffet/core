<?php

use App\Http\Controllers\Api\MenuController;
use Illuminate\Support\Facades\Route;


Route::prefix('api')->group(function () {
    Route::get('/menu', [MenuController::class, 'index']);
    Route::get('/menu/{id}', [MenuController::class, 'show']);
});
