<?php

use App\Models\User;
use App\Notifications\TestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use PHPUnit\Util\Test;

Route::prefix('account-center')->group(function () {
    require base_path('routes/api/v1/User/account_center.php');
});

Route::prefix('merchant')->middleware(['auth:sanctum', 'role:merchant'])->group(function () {
    require base_path('routes/api/v1/User/merchant.php');
});

// Route::prefix('driver')->middleware(['auth:sanctum', 'role:driver'])->group(function () {
//     require base_path('routes/api/v1/User/driver.php');
// });

Route::prefix('shipments')->middleware(['auth:sanctum', 'role:merchant'])->group(function () {
    require base_path('routes/api/v1/Shipment/merchant_shipment.php');
});

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::prefix('user-management')->group(function () {
        require base_path('routes/api/v1/Admin/user_management.php');
    });
});

Route::get('/me', function (Request $request) {

    $user = User::find(1);

    Notification::send($user, new TestNotification());
    
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function (Request $request) {
    return response()->json(["message" => "API is working fine"]);
});
