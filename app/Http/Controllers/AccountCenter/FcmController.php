<?php

namespace App\Http\Controllers\AccountCenter;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FcmController extends Controller
{
    public function setFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => ['required', 'string', 'unique:users,fcm_token']
        ]);

        $user = $request->user();

        $user->update([
            'fcm_token' => $request->fcm_token
        ]);

        return ApiResponse::success('Token saved successfully.');
    }

    public function removeFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => ['required', 'string']
        ]);

        $user = $request->user();

        if ($user->fcm_token !== $request->fcm_token) {
            return ApiResponse::fail('Invalid token.');
        }

        $user->update([
            'fcm_token' => null
        ]);

        return ApiResponse::success('Token removed successfully.');
    }
}
