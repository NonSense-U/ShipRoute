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
            'multiplier' => 1.20,
        ]);

        PricingMultiplier::create([
            'key' => 'refrigerated_vehicle',
            'multiplier' => 1.40,
        ]);

        PricingMultiplier::create([
            'key' => 'fragile_items',
            'multiplier' => 1.30,
        ]);

        PricingMultiplier::create([
            'key' => 'weight_factor_25',
            'multiplier' => 1.00,
        ]);

        PricingMultiplier::create([
            'key' => 'weight_factor_50',
            'multiplier' => 1.10,
        ]);

        PricingMultiplier::create([
            'key' => 'weight_factor_75',
            'multiplier' => 1.20,
        ]);

        PricingMultiplier::create([
            'key' => 'weight_factor_100',
            'multiplier' => 1.55,
        ]);
    }
}
