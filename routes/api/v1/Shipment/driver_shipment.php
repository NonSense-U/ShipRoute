<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::get('/available', [DriverController::class, 'getAvailableShipments']);
Route::post('/accept', [DriverController::class, 'acceptShipment']);
Route::patch('/cancel/{shipment}', [ShipmentController::class, 'cancelShipment']);
Route::post('/update-status', [DriverController::class, 'UpdateStatus']);
Route::post('/send-delivery-otp', [DriverController::class, 'sendDeliveryOTP']);
Route::post('/complete', [DriverController::class, 'completeTrip']);
Route::get('/logs', [DriverController::class, 'myShipmentLogs']);