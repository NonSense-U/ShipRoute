<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'goods_type' => ['required', 'string', 'max:255'],
            'weight' => ['required', 'numeric', 'min:0.1'],
            'vehicle_type' => ['required', 'string', 'max:255'],
            'vehicle_capacity_kg' => ['required', 'string', 'max:255'],
            'who_pays' => ['required', 'string', 'in:sender,receiver'],
            'scheduled_pickup_at' => ['nullable', 'date', 'after_or_equal:now'],
            'additional_details' => ['nullable', 'string'],
            'media' => ['nullable', 'array'],
            'media.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'route' => ['required', 'array'],
            'route.overview_polyline' => ['required', 'string'],
            'route.pick_up_lat' => ['required', 'string', 'max:50'],
            'route.pick_up_lng' => ['required', 'string', 'max:50'],
            'route.pick_up_location_details' => ['nullable', 'array'],
            'route.delivery_lat' => ['required', 'string', 'max:50'],
            'route.delivery_lng' => ['required', 'string', 'max:50'],
            'route.delivery_location_details' => ['nullable','array'],
            'route.distance' => ['required', 'numeric', 'min:0'],
            'route.duration_minutes' => ['required', 'integer', 'min:1'],
        ];
    }
}
