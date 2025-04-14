<?php

use App\Http\Controllers\Api\Portal\Admin\FeaturesController;
use App\Http\Controllers\Api\Portal\Admin\PlanController;
use App\Http\Controllers\Api\Portal\BillController;
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
// routes for admins
Route::middleware(['auth:api', 'only.superadmin'])->group(function () {
    // Bill route
    Route::get('/bills', [BillController::class, 'getAllBillsAndFeatures']);

    // Plan routes
    Route::post('/plans', [PlanController::class, 'store']);
    Route::post('/plans/assign-features', [PlanController::class, 'assignFeatures']);

    // Feature routes
    Route::apiResource('features', FeaturesController::class);
});

Route::prefix('{company_name}')->group(function () {
    Route::middleware(['auth:api', 'portal.access'])->group(function () {
        // Bill route
        Route::post('/bill', [BillController::class, 'store']);

        // Profile routes 
        Route::get('/profile', [ProfileController::class, 'index']);
        Route::put('/profile/{id}', [ProfileController::class, 'update']);

        // Department routes
        Route::get('/departments', [DepartmentController::class, 'index']);
        Route::get('/departments/{department}', [DepartmentController::class, 'show']);
        Route::post('/departments', [DepartmentController::class, 'store']);
        Route::put('/departments/{department}', [DepartmentController::class, 'update']);
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy']);
        Route::post('/departments/{department}/users', [DepartmentController::class, 'addUserToDepartment']);
        Route::delete('/departments/{department}/users', [DepartmentController::class, 'removeUserFromDepartment']);
        Route::get('/departments/{department}/users', [DepartmentController::class, 'getDepartmentUsers']);
        Route::put('/departments/{department}/users/role', [DepartmentController::class, 'updateUserRole']);

        // User routes
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });
});
