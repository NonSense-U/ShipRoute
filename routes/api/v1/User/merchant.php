<?php

use App\Http\Controllers\MerchantController;
use Illuminate\Support\Facades\Route;

Route::post('/shipments', [MerchantController::class, 'createShipment']);
Route::get('/shipments', [MerchantController::class, 'myShipments']);

