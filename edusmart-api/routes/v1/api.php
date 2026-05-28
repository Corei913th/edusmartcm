<?php

namespace App\Routes\v1;

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('login', [AuthController::class, 'login'])->withoutMiddleware('auth:sanctum');
    Route::post('register', [AuthController::class, 'register'])->withoutMiddleware('auth:sanctum');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});
