<?php

namespace Database\Seeders;

use App\Models\Shipment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shipment::factory(10)->create([
            'vehicle_type' => 'open',
        ]);
        Shipment::factory(10)->assigned()->create();
        Shipment::factory(10)->completed()->create([
            'driver_id' => 1,
            'merchant_id' => 1,
        ]);
        Shipment::factory(10)->completed()->create();
        Shipment::factory()->inTransit(1)->create([
            'driver_id' => 1,
            'merchant_id' => 1,
        ]);
    }
}
