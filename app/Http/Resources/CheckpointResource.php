<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckpointResource extends JsonResource
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
            'supervisor_name' => $this->supervisor_name,
            'supervisor_phone_number' => $this->supervisor_phone_number,
            'address' => $this->address,
            'street' => $this->street,
            'building_number' => $this->building_number,
            'notes' => $this->notes,
        ];
    }
}
