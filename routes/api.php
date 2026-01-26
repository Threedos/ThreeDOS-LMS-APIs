<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaskSubmissionController;
use App\Http\Controllers\Api\CouncilController;
use App\Http\Controllers\Api\CouncilSessionController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\CacheController;
use App\Http\Middleware\RateLimiting;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TeamMemberController;


//Routes
Route::get('/instance', function () {
    return response()->json(gethostname());
});
Route::post('login', [AuthController::class, 'login'])->name('login');
// Route::post('register', [AuthController::class, 'register']);
Route::post('forget-password', [AuthController::class, 'forgetPassword']);



// RateLimiting::class Commented due to testing phase
Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
   
   // Auth Routes
    Route::post('logout', [AuthController::class, 'logout']);

    // Dashboard Routes
    Route::get('users/dashboard', [UserController::class, 'dashboard'])->middleware('cache.response:3600');

    // Bulk Routes
    Route::post('team-members/bulk', [TeamMemberController::class, 'storeBulk']);
    Route::post('users/bulk', [UserController::class, 'BulkStore']);
    Route::post('attendances/bulk', [AttendanceController::class, 'bulkStore']);
    Route::get('me', [AuthController::class, 'me']);
    Route::get('/notifications', function (Request $request) {
        return $request->user()->notifications;
    })->middleware('cache.response:1800'); // Cache notifications for 30 minutes



    // Resource Routes
    Route::apiResource('councils', CouncilController::class)->middleware('cache.response:3600');
    Route::apiResource('users', UserController::class)->middleware('cache.response:3600');
    Route::apiResource('roles', RoleController::class)->middleware('cache.response:3600');
    Route::apiResource('tasks', TaskController::class)->middleware('cache.response:3600');
    Route::apiResource('sessions', CouncilSessionController::class)->middleware('cache.response:3600');
    Route::apiResource('attendances', AttendanceController::class)->middleware('cache.response:3600');
    Route::apiResource('task-submissions', TaskSubmissionController::class)->middleware('cache.response:3600');
    Route::apiResource('teams', TeamController::class);
    Route::apiResource('team-members', TeamMemberController::class);


    // Cache management routes
    Route::prefix('cache')->group(function () {
        Route::get('stats', [CacheController::class, 'stats']);
        Route::delete('endpoint', [CacheController::class, 'clearEndpointCache']);
        Route::delete('resource', [CacheController::class, 'clearResourceCache']);
        Route::delete('user/{userId}', [CacheController::class, 'clearUserCache']);
    });
});

