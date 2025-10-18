<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected authentication routes
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
});

// Email verification routes
Route::prefix('email')->group(function () {
    // Public verification route (for email links)
    Route::get('/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->name('verification.verify');
    
    // Protected verification routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/resend', [EmailVerificationController::class, 'resend'])
            ->name('verification.send');
        Route::get('/status', [EmailVerificationController::class, 'status']);
    });
});