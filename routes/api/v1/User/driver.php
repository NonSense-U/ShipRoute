<?php

use App\Http\Controllers\DriverController;
use Illuminate\Support\Facades\Route;


Route::get('/my-profits', [DriverController::class, 'myProfits']);
Route::patch('/location', [DriverController::class, 'updateLocation']);