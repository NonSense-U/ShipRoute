<?php

use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;

Route::get('/available', [DriverController::class, 'getAvailableShipments']);
Route::post('/accept', [DriverController::class, 'acceptShipment']);
Route::post('/start', [DriverController::class, 'startTrip']);
Route::post('/send-delivery-otp', [DriverController::class, 'sendDeliveryOTP']);
Route::post('/complete', [DriverController::class, 'completeTrip']);
Route::get('/log', [DriverController::class, 'myShipmentsLog']);