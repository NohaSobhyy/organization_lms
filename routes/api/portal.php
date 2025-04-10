<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Portal\UserController;
use App\Http\Controllers\Api\Portal\DepartmentController;
use App\Http\Controllers\Api\Portal\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('{company_name}')->group(function () {
    // Profile 
    Route::middleware(['auth:api', 'portal.access'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'index']);
        Route::put('/profile/{id}', [ProfileController::class, 'update']);
    });

    // Department routes
    Route::middleware(['auth:api', 'portal.access'])->group(function () {
        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::get('/departments/{department}', [DepartmentController::class, 'show']);
        Route::post('/departments', [DepartmentController::class, 'store']);
        Route::put('/departments/{department}', [DepartmentController::class, 'update']);
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy']);
        Route::post('/departments/{department}/users', [DepartmentController::class, 'addUserToDepartment']);
        Route::delete('/departments/{department}/users', [DepartmentController::class, 'removeUserFromDepartment']);
        Route::get('/departments/{department}/users', [DepartmentController::class, 'getDepartmentUsers']);
        Route::put('/departments/{department}/users/role', [DepartmentController::class, 'updateUserRole']);
    });

    // User routes
    Route::middleware(['auth:api', 'portal.access'])->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });
});