<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Jobs\SyncDriverGovernorate;
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
        $otp = (string) random_int(100000, 999999);
        Cache::put("otp_{$phoneNumber}", $otp, now()->addMinutes(10));
        $message = \App\Helpers\OTPMessageHelper::generateSignupOTPMessage($otp, 'ar');
        dispatch(new \App\Jobs\SendUltraMessageWhatsappOTP($phoneNumber, $otp, $message));
    }


    public function verifyOTP(string $phoneNumber, string $otp, ?string $user_id)
    {
        if (Cache::get('otp_' . $phoneNumber) !== $otp) {
            throw new \Exception('Invalid OTP code');
        }

        if ($user_id) {
            Cache::put('user' . $user_id . '_trusted', true);
        } else {
            Cache::put('trusted_number_' . $phoneNumber, true, now()->addMinutes(10));
        }
        Cache::forget('otp_' . $phoneNumber);
    }

    public function register(array $payload)
    {

        DB::beginTransaction();

        try {

            if (Cache::get('trusted_number_' . $payload['base']['phone_number']) !== true) {
                throw new \Exception('The phone number is not verified');
            }

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


    public function login(array $payload, string $role)
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

            if (!$user || !$user->hasRole($role ?? '')) {
                throw new AuthenticationException('There is no ' . $role . ' associated with the ' . $key . ' you provided.');
            } elseif (!Hash::check($payload['password'], $user->password)) {
                throw new AuthenticationException("Invalid credentials.");
            }

            $user->update([
                'last_login_at' => now(),
            ]);

            $response = [
                'id' => $user->profile?->id,
                'uid' => $user->id,
                'role' => $user->getRoleNames()->first(),
                'access_token' => $user->createToken('auth_token')->plainTextToken,
                'token_type' => 'Bearer',
            ];

            if ($role === 'driver') {
                dispatch(new SyncDriverGovernorate($user->driver->id));
            }

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
