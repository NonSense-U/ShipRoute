<?php

namespace Database\Seeders;

use App\Models\User;
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
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'phone_number' => 'admin',
            'password' => 'password',

        ]);

        $merchant = User::factory()->create([
            'full_name' => 'Merchant User',
            'email' => 'testUser1@gmail.com',
            'phone_number' => 'merchant',
            "password" => 'password',
        ]);

        $merchant->merchant()->create([
            'commercial_registration_number' => 'CRN123456',
            'address' => '123 Main St, City, Country',
        ]);

        $driver = User::factory()->create([
            'full_name' => 'Driver User',
            'email' => 'testUser3@gmail.com',
            'phone_number' => 'driver',
            "password" => 'password',
        ]); 

        $driver->driver()->create([
            'age' => 30,
            'gender' => 'male',
            'vehicle_type' => 'refrigerated',
            'vehicle_capacity_kg' => 5000,
        ]);

        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $admin->assignRole('admin');
        $merchant->assignRole('merchant');
        $driver->assignRole('driver');
    }
}
