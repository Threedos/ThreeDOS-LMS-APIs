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

//Routes
Route::get('/instance', function () {
    return response()->json(gethostname());
});
Route::post('login', [AuthController::class, 'login'])->name('login');
// Route::post('register', [AuthController::class, 'register']);

Route::middleware(['auth:api', \App\Http\Middleware\RateLimiting::class])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('councils', CouncilController::class);
    Route::post('users/bulk', [UserController::class, 'BulkStore']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('tasks', TaskController::class);
    Route::get(
        'task-submissions/user',
        [TaskSubmissionController::class, 'GetAllTaskSubmissionsForUser']
    );
    Route::apiResource('sessions', CouncilSessionController::class);
    Route::apiResource('attendances', AttendanceController::class);

    Route::get('/notifications', function (Request $request) {
        return $request->user()->notifications;
    });
    Route::apiResource('task-submissions', TaskSubmissionController::class);
});

