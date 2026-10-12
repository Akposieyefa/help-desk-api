<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IncidentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'loginMethod']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/all', [IncidentController::class, 'index']);
        Route::post('/create', [IncidentController::class, 'store']);
        Route::get('/single/{id}', [IncidentController::class, 'show']);
        Route::put('/update/{id}', [IncidentController::class, 'update']);
        Route::delete('/delete/{id}', [IncidentController::class, 'destroy']);

        Route::prefix('/auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logoutMethod']);
            Route::get('/profiles', [AuthController::class, 'profileMethod']);
            Route::post('/refresh', [AuthController::class, 'refreshMethod']);
        });
    });
});
