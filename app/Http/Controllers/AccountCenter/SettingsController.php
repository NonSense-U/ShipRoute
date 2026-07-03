<?php

namespace App\Http\Controllers\AccountCenter;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    use AuthorizesRequests;

    private UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function updateProfilePicture(Request $request)
    {
        $validated = $request->validate([
            'profile_picture' => ['required', 'image', 'max:2048'],
        ]);

        $this->userService->uploadProfilePicture($request->user(), $validated['profile_picture']);
        return ApiResponse::success('Profile picture updated successfully');
    }


    public function updatePassword(Request $request)
    {
        $validated = $request->validate(
            [
                'old_password' => ['nullable', 'string'],
                'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            ]
        );

        if (isset($validated['old_password'])) {
            if (!Hash::check($validated['old_password'], $request->user()->password)) {
                return ApiResponse::fail('Old password is incorrect', 422);
            }
        } else {
            $this->authorize('update-sensitive-info', $request->user());
        }

        $this->userService->resetPassword($request->user(), $validated['new_password']);
        return ApiResponse::success('Password updated successfully');
    }
}
