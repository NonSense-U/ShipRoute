<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
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
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'profile_picture_url' => $this->profile_picture_url,
            'merchant_profile' => $this->when(
                $this->hasRole('merchant') && $this->merchant,
                new MerchantProfileResource($this->merchant)
            ),
            'driver_profile' => $this->when(
                $this->hasRole('driver') && $this->driver,
                new DriverProfileResource($this->driver)
            ),
        ];
    }
}
