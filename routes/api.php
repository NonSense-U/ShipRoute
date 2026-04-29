<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::prefix('account-center')->group(function () {
    require base_path('routes/api/v1/User/account_center.php');
});


Route::get('me', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function(Request $request)
{
    return response()->json(["message" => "API is working fine"]);
});