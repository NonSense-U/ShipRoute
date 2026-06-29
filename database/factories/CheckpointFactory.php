<?php

namespace Database\Factories;

use App\Models\Checkpoint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Checkpoint>
 */
class CheckpointFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['pickup', 'delivery']),
            'supervisor_name' => fake()->name(),
            'supervisor_phone_number' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'street' => fake()->streetName(),
            'building_number' => fake()->buildingNumber(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function pickup(): static
    {
        return $this->state(fn () => [
            'type' => 'pickup',
        ]);
    }

    public function delivery(): static
    {
        return $this->state(fn () => [
            'type' => 'delivery',
        ]);
    }
}
