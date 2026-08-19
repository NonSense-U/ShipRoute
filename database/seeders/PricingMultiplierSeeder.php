<?php

namespace Database\Seeders;

use App\Models\PricingMultiplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PricingMultiplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        PricingMultiplier::create([
            'key' => 'app_share',
            'multiplier' => 0.20,
        ]);

        PricingMultiplier::create([
            'key' => 'night_shipping',
            'multiplier' => 0.20,
        ]);

        PricingMultiplier::create([
            'key' => 'refrigerated_vehicle',
            'multiplier' => 0.40,
        ]);

        PricingMultiplier::create([
            'key' => 'fragile_items',
            'multiplier' => 0.30,
        ]);

        PricingMultiplier::create([
            'key' => 'weight_factor_25',
            'multiplier' => 0.05,
        ]);

        PricingMultiplier::create([
            'key' => 'weight_factor_50',
            'multiplier' => 0.10,
        ]);

        PricingMultiplier::create([
            'key' => 'weight_factor_75',
            'multiplier' => 1.20,
        ]);

        PricingMultiplier::create([
            'key' => 'weight_factor_100',
            'multiplier' => 0.55,
        ]);
    }
}
