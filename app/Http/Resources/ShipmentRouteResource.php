<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentRouteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->loadMissing('checkpoints');
        $pickUpCheckpoint = $this->checkpoints->where('type', 'pickup')->first();
        $deliveryCheckpoint = $this->checkpoints->where('type', 'delivery')->first();

        return [
            'overview_polyline' => $this->overview_polyline,
            'pickup_lat' => $this->pickup_lat,
            'pickup_lon' => $this->pickup_lon,
            'pickup_checkpoint_details' => new CheckpointResource($pickUpCheckpoint),
            'delivery_lat' => $this->delivery_lat,
            'delivery_lon' => $this->delivery_lon,
            'delivery_checkpoint_details' => new CheckpointResource($deliveryCheckpoint),
            'distance' => (float) $this->distance,
            'duration_minutes' => $this->duration_minutes,
        ];
    }
}
