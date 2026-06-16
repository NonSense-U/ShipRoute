<?php

namespace App\Helpers;

use App\Models\PricingMultiplier;

class PricingMultiplierHelper
{
    public static function getRefrigeratedMultiplier(): float
    {
        return PricingMultiplier::query()->where('key', 'refrigerated_vehicle')->first()->multiplier ?? 1.4;
    }
    public static function getNightShippingMultiplier(): float
    {
        return PricingMultiplier::query()->where('key', 'night_shipping')->first()->multiplier ?? 1.2;
    }

    public static function getWeightMultiplier(float $weight_factor): float
    {
        if ($weight_factor <= 25) {
            return PricingMultiplier::query()->where('key', 'weight_factor_25')->first()->multiplier ?? 1.0;
        } elseif ($weight_factor <= 50) {
            return PricingMultiplier::query()->where('key', 'weight_factor_50')->first()->multiplier ?? 1.1;
        } elseif ($weight_factor <= 75) {
            return PricingMultiplier::query()->where('key', 'weight_factor_75')->first()->multiplier ?? 1.2;
        } else {
            return PricingMultiplier::query()->where('key', 'weight_factor_100')->first()->multiplier ?? 1.55;
        }
    }
}
