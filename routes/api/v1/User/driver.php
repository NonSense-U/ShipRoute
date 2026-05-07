<?php

use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;


Route::get('/shipments/available', [DriverController::class, 'getAvailableShipments']);
Route::post('/shipments/accept', [DriverController::class, 'acceptShipment']);
Route::post('/shipments/start', [DriverController::class, 'startTrip']);
Route::post('/shipments/send-delivery-otp', [DriverController::class, 'sendDeliveryOTP']);
Route::post('/shipments/complete', [DriverController::class, 'completeTrip']);
Route::patch('/location', [DriverController::class, 'updateLocation']);