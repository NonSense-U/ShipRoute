<?php

use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;


Route::get('/shipments/available', [DriverController::class, 'getAvailableShipments']);
Route::post('/shipments/accept', [DriverController::class, 'acceptShipment']);
Route::post('/shipments/start', [DriverController::class, 'startTrip']);
Route::patch('/location', [DriverController::class, 'updateLocation']);