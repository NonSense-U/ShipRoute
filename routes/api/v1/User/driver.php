<?php

use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;


Route::patch('/location', [DriverController::class, 'updateLocation']);