<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NewDriverRequest extends FormRequest
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
            'base' => ['required', 'array'],
            'base.full_name' => ['required', 'string', 'max:255'],
            'base.email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'base.phone_number' => ['required', 'string', 'max:255', 'unique:users,phone_number'],
            'base.id_card_number' => ['required', 'string', 'max:255', 'unique:users,id_card_number'],
            'base.password' => ['required', 'string', 'min:8', 'confirmed'],
            'profile' => ['required', 'array'],
            'profile.age' => ['required', 'integer', 'min:18'],
            'profile.gender' => ['required', 'string', 'in:male,female'],
            'profile.license_plate_number' => ['required', 'string', 'max:255'],
            'profile.vehicle_type' => ['required', 'string', 'max:255', 'in:open,closed,refrigerated'],
            'profile.vehicle_capacity_kg' => ['required', 'decimal:0,2', 'min:0'],
            'profile.driver_license_number' => ['required', 'string', 'max:255'],
            'profile.description' => ['nullable', 'string'],
            ];
    }
}
