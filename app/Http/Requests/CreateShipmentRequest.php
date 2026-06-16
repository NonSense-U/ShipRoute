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
            'who_pays' => ['required', 'string', 'in:sender,receiver'],
            'scheduled_pickup_at' => ['required', 'date', 'after_or_equal:now'],
            'additional_details' => ['nullable', 'string'],
            'media' => ['nullable', 'array'],
            'media.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'route' => ['required', 'array'],
            'route.overview_polyline' => ['required', 'string'],

            'route.pick_up_lat' => ['required', 'string', 'max:50'],
            'route.pick_up_lon' => ['required', 'string', 'max:50'],
            'route.pick_up_checkpoint_details' => ['required', 'array'],
            'route.pick_up_checkpoint_details.supervisor_name' => ['required', 'string', 'max:255'],
            'route.pick_up_checkpoint_details.supervisor_phone_number' => ['required', 'string', 'max:20'],
            'route.pick_up_checkpoint_details.address' => ['nullable', 'string', 'max:255'],
            'route.pick_up_checkpoint_details.street' => ['nullable', 'string', 'max:255'],
            'route.pick_up_checkpoint_details.building_number' => ['nullable', 'string', 'max:50'], 
            'route.pick_up_checkpoint_details.notes' => ['nullable', 'string', 'max:255'],

            'route.delivery_lat' => ['required', 'string', 'max:50'],
            'route.delivery_lon' => ['required', 'string', 'max:50'],
            'route.delivery_checkpoint_details' => ['required', 'array'],
            'route.delivery_checkpoint_details.supervisor_name' => ['required', 'string', 'max:255'],
            'route.delivery_checkpoint_details.supervisor_phone_number' => ['required', 'string', 'max:20'],
            'route.delivery_checkpoint_details.address' => ['nullable', 'string', 'max:255'],
            'route.delivery_checkpoint_details.street' => ['nullable', 'string', 'max:255'],
            'route.delivery_checkpoint_details.building_number' => ['nullable', 'string', 'max:50'],
            'route.delivery_checkpoint_details.notes' => ['nullable', 'string', 'max:255'],
            'route.distance' => ['required', 'numeric', 'min:0'],
            'route.duration_minutes' => ['required', 'integer', 'min:1'],
        ];
    }
}
