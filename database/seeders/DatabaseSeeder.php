<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Merchant;
use App\Models\User;
use Database\Factories\DriverFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $admin = User::factory()->create([
            'full_name' => 'Mohammad',
            'email' => 'test@example.com',
            'phone_number' => '+963994398034',
            'password' => 'password',

        ]);

        $merchant = User::factory()->create([
            'full_name' => 'Sedra',
            'email' => 'testUser1@gmail.com',
            'phone_number' => '+963994191080',
            "password" => 'password',
            'id_card_number' => '0987654321',
        ]);

        $merchant->merchant()->create([
            'commercial_registration_number' => 'CRN123456',
            'address' => '123 Main St, City, Country',
        ]);

        $driver = User::factory()->create([
            'full_name' => 'Khaled',
            'email' => 'testUser3@gmail.com',
            'phone_number' => '+963932560755',
            "password" => 'password',
            'id_card_number' => '1111111111',
        ]); 

        $driver->driver()->create([
            'age' => 30,
            'gender' => 'male',
            'vehicle_type' => 'refrigerated',
            'vehicle_size' => 'small',
            'vehicle_capacity_kg' => 900,
            'license_plate_number' => 'ABC123',
            'driver_license_number' => 'DL123456',
            'current_lat' => '33.509194',
            'current_lon' => '36.275145'
        ]);

        $this->call([
            RolesAndPermissionsSeeder::class,
            VehicleSizePricingSeeder::class,
            PricingMultiplierSeeder::class,
            ShipmentSeeder::class,
            RatingSeeder::class,
        ]);

        $admin->assignRole('admin');
        $merchant->assignRole('merchant');
        $driver->assignRole('driver');
    }
}
