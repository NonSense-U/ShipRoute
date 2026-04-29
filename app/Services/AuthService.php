<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Throwable;

class AuthService
{
    public function sendOTP(string $phoneNumber)
    {
        $otpCode = rand(100000, 999999);
        Cache::add("otp_{$phoneNumber}", $otpCode, now()->addMinutes(5));
        //TODO send OTP code to the user's phone number using an SMS gateway
    }


    public function verifyOTP(array $payload)
    {
        $user = User::query()
            ->where('phone_number', $payload['phone_number'])
            ->firstOrFail();

        if (Cache::get("otp_{$payload['phone_number']}") !== $payload['otp_code']) {
            throw new \Exception('Invalid OTP code');
        }

        $user->update([
            'phone_verified_at' => now(),
            'otp_code' => null,
        ]);

        Cache::forget("otp_{$payload['phone_number']}");
    }

    public function register(array $payload)
    {

        DB::beginTransaction();

        try {
            $data = collect();
            $user = User::create($payload['base']);;
            $user->assignRole('merchant');
            $user->merchant()->create($payload['profile']);
            
            $data['user'] = $user;

            if (!empty($payload['login']) && $payload['login']) {
                $data['token'] = $user->createToken('auth_token')->plainTextToken;
            }

            DB::commit();
            return $data;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function login(array $payload)
    {
        try {
            $user = null;
            $key = '';
            if (isset($payload['email'])) {
                $key = 'email';
                $user = User::where('email', $payload['email'])->first();
            } elseif (isset($payload['phone_number'])) {
                $key = 'phone_number';
                $user = User::where('phone_number', $payload['phone_number'])->first();
            }

            if (!$user) {
                throw new AuthenticationException('There is no user associated with the ' . $key . ' you provided.');
            } elseif (!Hash::check($payload['password'], $user->password)) {
                throw new AuthenticationException("Invalid credentials.");
            }

            $response['id'] = $user->id;
            $response['username'] = $user->username;
            $response['role'] = $user->getRoleNames()->first();
            $response['access_token'] = $user->createToken('auth_token')->plainTextToken;
            $response['token_type'] = 'Bearer';

            return $response;
        } catch (Throwable $e) {
            throw $e;
        }
    }


    public function logout(User $user)
    {
        try {
            /** @var \Laravel\Sanctum\PersonalAccessToken|null $token */
            $token = $user->currentAccessToken();
            $token?->delete();
            $user->update(['fcm_token' => null]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
