<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantPreviewResource extends JsonResource
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
            'address' => $this->address,
        ];
    }
}
