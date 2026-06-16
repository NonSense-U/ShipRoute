<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
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
            'goods_type' => $this->goods_type,
            'weight' => (float) $this->weight,
            'vehicle_type' => $this->vehicle_type,
            'vehicle_size' => $this->vehicle_size,
            'night_shipping' => $this->is_night_shipping,
            'who_pays' => $this->who_pays,
            'price' => (float) $this->price,
            'additional_details' => $this->additional_details,
            'status' => $this->status,
            'pickup_at' => $this->scheduled_pickup_at,
            'picked_up_at' => $this->picked_up_at,
            'delivered_at' => $this->delivered_at,

            'media_urls' => $this->media ?? [],

            'merchant' => new MerchantPreviewResource($this->whenLoaded('merchant')),
            'driver' => new DriverPreviewResource($this->whenLoaded('driver')),
            'route' => new ShipmentRouteResource($this->whenLoaded('route')),
            'created_at' => $this->created_at,
        ];
    }
}
