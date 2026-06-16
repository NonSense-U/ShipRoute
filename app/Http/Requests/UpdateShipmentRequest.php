<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipment' => ['sometimes', 'array'],
            'shipment.goods_type' => ['sometimes', 'string', 'max:255'],
            'shipment.weight' => ['sometimes', 'numeric', 'min:0.1'],
            'shipment.vehicle_type' => ['sometimes', 'string', 'max:255'],
            'shipment.who_pays' => ['sometimes', 'string', 'in:sender,receiver'],
            'shipment.scheduled_pickup_at' => ['sometimes', 'nullable', 'date', 'after_or_equal:now'],
            'shipment.additional_details' => ['sometimes', 'nullable', 'string'],

            'media' => ['sometimes', 'nullable', 'array'],
            'media.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'route' => ['sometimes', 'array'],
            'route.overview_polyline' => ['sometimes', 'string'],
            'route.pick_up_lat' => ['sometimes', 'string', 'max:50'],
            'route.pick_up_lon' => ['sometimes', 'string', 'max:50'],
            'route.pick_up_location_details' => ['sometimes', 'nullable', 'array'],
            'route.delivery_lat' => ['sometimes', 'string', 'max:50'],
            'route.delivery_lon' => ['sometimes', 'string', 'max:50'],
            'route.delivery_location_details' => ['sometimes', 'nullable', 'array'],
            'route.distance' => ['sometimes', 'numeric', 'min:0'],
            'route.duration_minutes' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
