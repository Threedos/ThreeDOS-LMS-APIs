<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\RoleController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\TaskController;
use App\Http\Controllers\api\TaskSubmissionController;
use App\Http\Controllers\api\CouncilController;




//Routes

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('register', [AuthController::class, 'register']);

Route::apiResource('roles', RoleController::class);
Route::apiResource('councils', CouncilController::class);
Route::apiResource('tasks', TaskController::class);
Route::apiResource('task-submissions', TaskSubmissionController::class);
Route::apiResource('users', UserController::class);
