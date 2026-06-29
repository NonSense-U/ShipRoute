<?php

namespace Database\Factories;

use App\Helpers\VehicleHelper;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $capacity = fake()->randomFloat(2, 100, 4000);
        return [
            'user_id' => \App\Models\User::factory()->driver(),
            'age' => fake()->numberBetween(18, 65),
            'gender' => fake()->randomElement(['male', 'female']),
            'current_governorate' => 'محافظة دمشق',
            'current_lat' => fake()->latitude('33.509053699780374','33.509340137794005'),
            'current_lon' => fake()->longitude('36.26758575439454','36.27754211425782'),
            'last_location_at' => fake()->optional()->dateTimeBetween('-7 days', 'now'),
            'vehicle_type' => fake()->randomElement(['refrigerated', 'open', 'covered']),
            'vehicle_size' => VehicleHelper::getVehicleSize($capacity),
            'vehicle_capacity_kg' => $capacity,
            'license_plate_number' => strtoupper(fake()->bothify('???-####')),
            'driver_license_number' => strtoupper(fake()->bothify('DL########')),
            'description' => fake()->optional()->paragraph(),
        ];
    }
}
