<?php

namespace App\Http\Controllers\AccountCenter;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\MerchantRegisterRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(MerchantRegisterRequest $request)
    {
        $data = $this->authService->register($request->validated());
        return ApiResponse::success('Merchant registered successfully', $data, 201);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'string', 'max:255'],
        ]);

        $this->authService->sendOtp($request->input('phone_number'));
        return ApiResponse::success('OTP sent successfully');
    }

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'string', 'max:255'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $this->authService->verifyOtp($validated);
        return ApiResponse::success('OTP verified successfully');
    }

    public function login(LoginRequest $request, string $role)
    {
        $response = $this->authService->login($request->validated(), $role);
        return ApiResponse::success('Login successful', $response);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
        return ApiResponse::success('Logout successful');
    }
}
