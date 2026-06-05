<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DrainageController;
use App\Http\Controllers\DrainagePhotoController;
use App\Http\Controllers\FloodLocationController;
use App\Http\Controllers\FloodPhotoController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::apiResource('drainages', DrainageController::class);
    Route::post('drainages/{drainage}/photos', [DrainagePhotoController::class, 'store']);
    Route::delete('drainage-photos/{photo}', [DrainagePhotoController::class, 'destroy']);
    Route::get('drainages/{drainage}/photos', [DrainagePhotoController::class, 'index']);

    Route::apiResource('flood-locations', FloodLocationController::class);
    Route::post('flood-locations/{floodLocation}/photos', [FloodPhotoController::class, 'store']);
    Route::delete('flood-photos/{photo}', [FloodPhotoController::class, 'destroy']);
    Route::get('flood-locations/{floodLocation}/photos', [FloodPhotoController::class, 'index']);

    Route::apiResource('news', NewsController::class);

    Route::middleware('role:admin|super_admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::put('settings', [SettingController::class, 'update']);
        Route::get('settings', [SettingController::class, 'show']);
    });
});

Route::get('/map/drainages', [MapController::class, 'drainages']);
Route::get('/map/floods', [MapController::class, 'floods']);
Route::get('/map/districts', [MapController::class, 'districts']);
Route::get('/map/villages', [MapController::class, 'villages']);

Route::get('/public/news', [NewsController::class, 'public']);
