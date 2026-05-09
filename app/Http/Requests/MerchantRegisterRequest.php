<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantRegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'base' => ['required', 'array'],
            'base.full_name' => ['required', 'string', 'max:255'],
            'base.email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'base.phone_number' => ['required', 'string', 'max:255', 'unique:users,phone_number'],
            'base.password' => ['required', 'string', 'min:8', 'confirmed'],
            'profile' => ['required', 'array'],
            'profile.commercial_registration_number' => ['required', 'string', 'max:255'],
            'profile.address' => ['required', 'string', 'max:255'],
            'login' => ['nullable', 'boolean']
        ];
    }
}
