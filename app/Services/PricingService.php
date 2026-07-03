<?php

namespace App\Services;

use App\Models\VehicleSizePricing;

class PricingService
{
    public function editVehiclePricing(array $payload)
    {
        $vehicle_pricing = VehicleSizePricing::query()->where('size', $payload['vehicle_size'])->first();
        if (isset($payload['per_km_fee'])) {
            $vehicle_pricing->per_km_fee = $payload['per_km_fee'];
        }
        if (isset($payload['starting_fee'])) {
            $vehicle_pricing->starting_fee = $payload['starting_fee'];
        }
        $vehicle_pricing->save();

        return $vehicle_pricing;
    }

    public function editPricingMultiplier(array $payload)
    {
        $pricing_multiplier = \App\Models\PricingMultiplier::query()->where('key', $payload['multiplier'])->first();
        $pricing_multiplier->multiplier = $payload['value'];
        $pricing_multiplier->save();
        return $pricing_multiplier;
    }
}
