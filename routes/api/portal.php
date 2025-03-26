<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Organization\UserController;
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

Route::group(['middleware' => ['check_mobile_app', 'share', 'check_maintenance']], function () {

    // Portal Authentication Routes
    // Route::post('/login', [PortalAuthController::class, 'login']);  
    // Route::post('/logout', [PortalAuthController::class, 'logout'])->middleware('auth:api');  


    Route::get('/{company_name}', ['PortalController@portal_login'])->name('portal.login');

    Route::group(['prefix' => '{company_name}'], function () {
        // User CRUD
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        // Department CRUD
        Route::get('/departments', ['departmentController@index']);
        Route::post('/departments', ['departmentController@store']);
        Route::put('/departments/{department}', ['departmentController@update']);
        Route::delete('/departments/{department}', ['departmentController@destroy']);
        Route::post('/departments/{department}/add-user', ['departmentController@addUserToDepartment']);
    });
});