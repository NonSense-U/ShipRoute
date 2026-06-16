<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverProfileResource extends JsonResource
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
            'age' => $this->age,
            'gender' => $this->gender,
            'vehicle_type' => $this->vehicle_type,
            'vehicle_size' => $this->vehicle_size,
            'vehicle_capacity_kg' => $this->vehicle_capacity_kg,
            'description' => $this->description,
        ];
    }
}
