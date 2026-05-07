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
            'current_lat' => ['required', 'string', 'max:50'],
            'current_lng' => ['required', 'string', 'max:50'],
        ];
    }
}
