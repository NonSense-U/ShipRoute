<?php

use App\Http\Controllers\Admin\DriverManagementController;
use App\Http\Controllers\Admin\MerchantManagementController;
use Illuminate\Support\Facades\Route;

Route::apiResource('drivers', DriverManagementController::class);
Route::get('/drivers/{driverId}/profits', [DriverManagementController::class, 'getDriverProfits']);

Route::prefix('merchants')->group(function () {
    Route::get('/', [MerchantManagementController::class, 'index']);
    Route::delete('/{merchantId}', [MerchantManagementController::class, 'destroy']);
});