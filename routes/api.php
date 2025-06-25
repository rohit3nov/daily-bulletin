<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\UserPreferenceController;

Route::prefix('auth')->group(function () {
    Route::controller(AuthController::class)
        ->group(function () {
            Route::post('register', 'register');
            Route::post('login', 'login');
            Route::post('forgot-password', 'forgotPassword');
            Route::post('reset-password', 'resetPassword');
        });

    Route::middleware('auth:sanctum')
        ->controller(AuthController::class)
        ->group(function () {
            Route::post('logout', 'logout');
            Route::post('change-password', 'changePassword');
        });
});

Route::prefix('user')
    ->middleware('auth:sanctum')
    ->controller(UserController::class)
    ->group(function () {
        Route::get('/', 'user');
        Route::put('update', 'updateProfile');
        Route::put('feed', 'updateProfile');
    });

Route::prefix('preferences')
    ->middleware('auth:sanctum')
    ->controller(UserPreferenceController::class)
    ->group(function () {
        Route::get('/', 'show');
        Route::put('/', 'update');
    });
Route::get('articles/personalized', [ArticleController::class, 'personalized'])->middleware('auth:sanctum');


Route::apiResource('articles', ArticleController::class)->only(['index', 'show']);
