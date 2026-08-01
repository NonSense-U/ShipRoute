<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\MerchantController::class, 'myShipments']);
Route::post('/', [\App\Http\Controllers\ShipmentController::class, 'createShipment']);
Route::get('/calculate-price', [\App\Http\Controllers\ShipmentController::class, 'getPrice']);
Route::patch('/{shipment}', [\App\Http\Controllers\ShipmentController::class, 'updateShipment']);
Route::patch('/cancel/{shipment}', [\App\Http\Controllers\ShipmentController::class, 'cancelShipment']);