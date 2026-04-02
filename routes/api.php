<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');
    Route::post('/reset/otp', 'resetOtp')->name('reset.otp');
    Route::post('/reset/password', 'resetPassword')->name('reset.password');
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/otp', 'Otp')->name('otp');
        Route::post('/verify-otp', 'verifyOtp')->name('otp.verify');
        Route::post('/logout', 'logout')->name('logout');
    });
});
