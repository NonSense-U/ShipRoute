<?php

use App\Http\Controllers\RatingController;
use App\Http\Resources\UserProfileResource;
use App\Http\Controllers\Admin\DashboardController;
use App\Notifications\TestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

Route::prefix('account-center')->group(function () {
    require base_path('routes/api/v1/User/account_center.php');
});

Route::prefix('merchant')->middleware(['auth:sanctum', 'role:merchant'])->group(function () {
    require base_path('routes/api/v1/User/merchant.php');

    Route::prefix('shipments')->group(function () {
        require base_path('routes/api/v1/Shipment/merchant_shipment.php');
    });
});

Route::prefix('driver')->middleware(['auth:sanctum', 'role:driver'])->group(function () {
    require base_path('routes/api/v1/User/driver.php');

    Route::prefix('shipments')->group(function () {
        require base_path('routes/api/v1/Shipment/driver_shipment.php');
    });
});

Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics']);

    Route::prefix('user-management')->group(function () {
        require base_path('routes/api/v1/Admin/user_management.php');
    });

    Route::prefix('shipment-management')->group(function () {
        require base_path('routes/api/v1/Admin/shipment_management.php');
    });

    Route::prefix('pricing-management')->group(function () {
        require base_path('routes/api/v1/Admin/pricing_management.php');
    });
});

Route::prefix('ratings')->middleware(['auth:sanctum', 'role:merchant|driver|admin'])->group(function () {
    Route::post('/shipments/{shipment}', [RatingController::class, 'store']);
    Route::get('/users/{user}', [RatingController::class, 'summary']);
    Route::get('/users/{user}/ratings', [RatingController::class, 'getReceivedRatings']);
    Route::get('/users/{user}/given-ratings', [RatingController::class, 'getGivenRatings']);
    Route::get('/my-given-ratings', [RatingController::class, 'myGivenRatings']);
});

Route::prefix('notifications')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [\App\Http\Controllers\NotificationController::class, 'getNotifications']);
    Route::post('/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
});

Route::get('/me', function (Request $request) {
    return new UserProfileResource(
        $request->user()->loadMissing(['merchant', 'driver', 'roles'])
    );
})->middleware('auth:sanctum');

Route::get('/test', function (Request $request) {
    return response()->json(["message" => "API is working fine"]);
});

Route::get('/notification-test', function(Request $request){
    Notification::send($request->user(), new TestNotification());
    return response()->json(["message" => "Notification sent successfully"]);
})->middleware('auth:sanctum');