<?php

use App\Http\Controllers\Admin\DriverManagementController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::apiResource('drivers', DriverManagementController::class)->middleware(['auth:sanctum', 'role:admin']);