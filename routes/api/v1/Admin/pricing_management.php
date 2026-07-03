<?php

use Illuminate\Support\Facades\Route;

Route::get('/vehicle-pricing', [\App\Http\Controllers\Admin\PricingController::class, 'getVehiclePricing']);
Route::get('/pricing-multiplier', [\App\Http\Controllers\Admin\PricingController::class, 'getPricingMultiplier']);
Route::patch('/vehicle-pricing', [\App\Http\Controllers\Admin\PricingController::class, 'editVehiclePricing']);
Route::patch('/pricing-multiplier', [\App\Http\Controllers\Admin\PricingController::class, 'editMultiplier']);