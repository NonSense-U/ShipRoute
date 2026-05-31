<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'base' => ['nullable', 'array'],
            'base.full_name' => ['nullable', 'string', 'max:255'],
            'base.email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'base.phone_number' => ['nullable', 'string', 'max:255', 'unique:users,phone_number'],
            'base.password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile' => ['nullable', 'array'],
            'profile.age' => ['nullable', 'integer', 'min:18'],
            'profile.gender' => ['nullable', 'string', 'in:male,female'],
            'profile.license_plate_number' => ['nullable', 'string', 'max:255'],
            //TODO Sepcify the allowed vehicle types
            'profile.vehicle_type' => ['nullable', 'string', 'max:255'],
            'profile.driver_license_number' => ['nullable', 'string', 'max:255'],
            'profile.description' => ['nullable', 'string'],
        ];
    }
}
