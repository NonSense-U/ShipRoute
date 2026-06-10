<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDriverLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipment_id' => ['nullable', 'integer', 'exists:shipments,id'],
            'current_lat' => ['required', 'string', 'max:50'],
            'current_lon' => ['required', 'string', 'max:50'],
        ];
    }
}
