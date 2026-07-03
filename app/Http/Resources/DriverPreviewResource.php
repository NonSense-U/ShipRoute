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
            'uid' => $this->user->id,
            'full_name' => $this->user->full_name,
            'email' => $this->user->email,
            'phone_number' => $this->user->phone_number,
            'vehicle_type' => $this->vehicle_type,
            'vehicle_size' => $this->vehicle_size,
            'current_lat' => $this->current_lat,
            'current_lon' => $this->current_lon,
            'profile_picture_url' => $this->user->profile_picture_url,
        ];
    }
}
