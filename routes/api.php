<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkLocationController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Any authenticated user
    Route::apiResource('work-locations', WorkLocationController::class)
        ->only(['index', 'show']);

    Route::apiResource('tasks', TaskController::class)
        ->only(['index', 'show']);

    Route::middleware('admin')->group(function () {

        Route::apiResource('work-locations', WorkLocationController::class)
            ->except(['index', 'show']);

        Route::apiResource('tasks', TaskController::class)
            ->except(['index', 'show']);
    });
});