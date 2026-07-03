<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EditPricingMultiplierRequest extends FormRequest
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
            'multiplier' => ['required', 'string', 'in:refrigerated_vehicle,night_shipping,weight_factor_25,weight_factor_50,weight_factor_75,weight_factor_100'],
            'value' => ['required', 'decimal:0,2', 'min:1'],
        ];
    }
}
