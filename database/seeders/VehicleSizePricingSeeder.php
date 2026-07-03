<?php

namespace Database\Seeders;

use App\Models\VehicleSizePricing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleSizePricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VehicleSizePricing::create([
            'size' => 'small',
            'max_capacity_kg' => 900,
            'starting_fee' => 10.00,
            'per_km_fee' => 0.40,
        ]);

        VehicleSizePricing::create([
            'size' => 'medium',
            'max_capacity_kg' => 2000,
            'starting_fee' => 15.00,
            'per_km_fee' => 0.60,
        ]);

        VehicleSizePricing::create([
            'size' => 'large',
            'max_capacity_kg' => 4000,
            'starting_fee' => 20.00,
            'per_km_fee' => 0.90,
        ]);
    }
}
