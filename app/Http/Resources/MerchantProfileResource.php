<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantProfileResource extends JsonResource
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
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'commercial_registration_number' => $this->commercial_registration_number,
            'id_card_number' => $this->user->id_card_number,
            'rating_info' => $this->user->rating_info,
            'shipments_count' => $this->whenCounted('shipments'),
            'address' => $this->address,
        ];
    }
}
