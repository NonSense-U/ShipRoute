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
            'phone_number' => '121212',
            'password' => 'password',

        ]);

        $merchant = User::factory()->create([
            'full_name' => 'Merchant User',
            'email' => 'testUser1@gmail.com',
            'phone_number' => '123123123',
            "password" => 'password',
        ]);

        $driver = User::factory()->create([
            'full_name' => 'Driver User',
            'email' => 'testUser3@gmail.com',
            'phone_number' => '1234567890',
            "password" => 'password',
        ]); 

        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $admin->assignRole('admin');
        $merchant->assignRole('merchant');
        $driver->assignRole('driver');
    }
}
