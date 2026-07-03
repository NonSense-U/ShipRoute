<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EditVehiclePricingRequest extends FormRequest
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
            'vehicle_size' => ['required', 'string','in:small,medium,large'],
            'per_km_fee' => ['nullable', 'decimal:0,2'],
            'starting_fee' => ['nullable', 'decimal:0,2']

        ];
    }
}
