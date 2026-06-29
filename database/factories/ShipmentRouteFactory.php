<?php

namespace Database\Factories;

use App\Models\Checkpoint;
use App\Models\ShipmentRoute;
use Illuminate\Database\Eloquent\Factories\Factory;

use function Illuminate\Log\log;

/**
 * @extends Factory<ShipmentRoute>
 */
class ShipmentRouteFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'overview_polyline' => fake()->sha1(),
            'pickup_governorate' => 'محافظة دمشق',
            'pickup_lat' => fake()->latitude('33.509053699780374', '33.509340137794005'),
            'pickup_lon' => fake()->longitude('36.26758575439454', '36.27754211425782'),
            'delivery_lat' => fake()->latitude('33.509053699780374', '33.509340137794005'),
            'delivery_lon' => fake()->longitude('36.26758575439454', '36.27754211425782'),
            'distance' => fake()->randomFloat(2, 1, 100),
            'duration_minutes' => fake()->numberBetween(5, 240),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (ShipmentRoute $shipmentRoute) {
            log('Creating checkpoints for ShipmentRoute ID: ' . $shipmentRoute->id);
            $shipmentRoute->checkpoints()->createMany(
                [
                    Checkpoint::factory()->pickup()->make()->toArray(),
                    Checkpoint::factory()->delivery()->make()->toArray()
                ]
            );
        });
    }
}
