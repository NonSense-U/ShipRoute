<?php

use App\Http\Controllers\AccountCenter\SettingsController;
use Illuminate\Support\Facades\Route;

Route::post('update-profile-picture', [SettingsController::class, 'updateProfilePicture']);
Route::post('update-password', [SettingsController::class, 'updatePassword']);
