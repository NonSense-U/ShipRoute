<?php

namespace App\Http\Resources;

use App\Helpers\PricingMultiplierHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $app_share = $this->price * PricingMultiplierHelper::getMultiplier('app_share');
        return [
            'merchant' => $this->whenLoaded('merchant', function () {
                return new MerchantPreviewResource($this->merchant);
            }),
            'shipment_id' => (string) $this->id,
            'status' => $this->status,
            'total_price' => $this->price,
            'pure_profit' => $this->price - $app_share,
            'app_share' => $app_share,
            'processed' => $this->driver->last_processed_at? $this->created_at->lte($this->driver->last_processed_at) : false,
        ];
    }
}
