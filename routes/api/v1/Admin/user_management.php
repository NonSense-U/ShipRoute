<?php

use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::post('/drivers', [UserManagementController::class, 'addDriver']);
Route::delete('/drivers/{driverId}', [UserManagementController::class, 'deleteDriver']);