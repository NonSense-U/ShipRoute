<?php

use App\Http\Controllers\AccountCenter\AuthController;
use Illuminate\Support\Facades\Route;



Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);
Route::delete('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Route::patch('/fcm-token', [FcmController::class, 'setFcmToken'])->middleware('auth:sanctum');
// Route::delete('/fcm-token', [FcmController::class, 'removeFcmToken'])->middleware('auth:sanctum');