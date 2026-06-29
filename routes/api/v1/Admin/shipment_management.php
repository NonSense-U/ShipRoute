<?php

use App\Http\Controllers\Admin\ShipmentManagementController;
use Illuminate\Support\Facades\Route;


Route::get('/', [ShipmentManagementController::class, 'index']);