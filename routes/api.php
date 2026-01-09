<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\api\RoleController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\TaskController;
use App\Http\Controllers\api\TaskSubmissionController;
use App\Http\Controllers\api\CouncilController;
use App\Http\Controllers\api\CouncilSessionController;
use App\Http\Controllers\api\AttendanceController;
use App\Http\Controllers\api\CacheController;

//Routes
Route::get('/instance', function () {
    return response()->json(gethostname());
});
Route::post('login', [AuthController::class, 'login'])->name('login');
// Route::post('register', [AuthController::class, 'register']);

Route::middleware(['auth:api', \App\Http\Middleware\RateLimiting::class])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Apply cache middleware to resource routes (caches GET requests for 1 hour = 3600 seconds)
    Route::apiResource('councils', CouncilController::class)->middleware('cache.response:3600');
    Route::post('users/bulk', [UserController::class, 'BulkStore']);
    Route::apiResource('users', UserController::class)->middleware('cache.response:3600');
    Route::apiResource('roles', RoleController::class)->middleware('cache.response:3600');
    Route::apiResource('tasks', TaskController::class)->middleware('cache.response:3600');

    
    Route::apiResource('sessions', CouncilSessionController::class)->middleware('cache.response:3600');
    Route::apiResource('attendances', AttendanceController::class)->middleware('cache.response:3600');
    Route::post('attendances/bulk', [AttendanceController::class, 'bulkStore']);
    Route::get('/notifications', function (Request $request) {
        return $request->user()->notifications;
    })->middleware('cache.response:1800'); // Cache notifications for 30 minutes

    Route::apiResource('task-submissions', TaskSubmissionController::class)->middleware('cache.response:3600');
    Route::get(
        'task-submissions/user',
        [TaskSubmissionController::class, 'GetAllTaskSubmissionsForUser']
    )->middleware('cache.response:3600');

    // Cache management routes
    Route::prefix('cache')->group(function () {
        Route::get('stats', [CacheController::class, 'stats']);
        Route::delete('endpoint', [CacheController::class, 'clearEndpointCache']);
        Route::delete('resource', [CacheController::class, 'clearResourceCache']);
        Route::delete('user/{userId}', [CacheController::class, 'clearUserCache']);
    });
});

