<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverPreviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->user->full_name,
            'email' => $this->user->email,
            'phone_number' => $this->user->phone_number,
            'age' => $this->age,
            'gender' => $this->gender,
            'vehicle_type' => $this->vehicle_type,
            'vehicle_size' => $this->vehicle_size,
            'vehicle_capacity_kg' => $this->vehicle_capacity_kg,
            'license_plate_number' => $this->license_plate_number,
            'shipments_count' => $this->whenCounted('shipments'),
            'rating_info' => $this->user->rating_info,
            'description' => $this->description,
        ];
    }
}
