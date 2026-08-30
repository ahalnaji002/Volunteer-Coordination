<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\VolunteerController;
use App\Http\Controllers\WorkLocationController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::middleware('volunteer')->group(function () {
        Route::get('/my-assignments', [AssignmentController::class, 'myAssignments']);
        Route::get('/me', [VolunteerController::class, 'me']);
        Route::match(['put', 'patch'], '/me', [VolunteerController::class, 'updateMe']);
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

        Route::apiResource('volunteers', VolunteerController::class);

        Route::apiResource('assignments', AssignmentController::class);
    });
});
