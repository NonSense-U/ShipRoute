<?php

namespace Database\Factories;

use App\Helpers\GovernorateQueueHelper;
use App\Helpers\VehicleHelper;
use App\Models\Driver;
use App\Models\Merchant;
use App\Models\Shipment;
use App\Models\ShipmentRoute;
use Illuminate\Database\Eloquent\Factories\Factory;

use function Illuminate\Log\log;

/**
 * @extends Factory<Shipment>
 */
class ShipmentFactory extends Factory
{

    public function configure(): static
    {
        return $this->afterCreating(function (Shipment $shipment) {
            ShipmentRoute::factory()->create([
                'shipment_id' => $shipment->id,
            ]);
        });
    }
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $weight = fake()->randomFloat(2, 200, 4000);
        return [
            'merchant_id' => 1,
            'goods_type' => fake()->randomElement(['electronics', 'food', 'furniture']),
            'vehicle_type' => fake()->randomElement(['bike', 'car', 'van', 'truck']),
            'vehicle_size' => VehicleHelper::getVehicleSize($weight),
            'who_pays' => fake()->randomElement(['sender', 'receiver']),
            'weight' => $weight,
            'additional_details' => fake()->optional()->paragraph(),
            'is_night_shipping' => fake()->boolean(),
            'scheduled_pickup_at' => fake()->dateTimeBetween('now', '+1 month'),
            'price' => fake()->randomFloat(2, 100, 3000),
            'status' => 'scheduled',
            'created_at' => fake()->dateTimeBetween('-12 month', 'now'),
        ];
    }

    public function assigned(?int $driver_id = null, ?string $status = null): static
    {
        return $this->afterCreating(function (Shipment $shipment) use ($driver_id, $status) {
            $shipment->driver_id = $driver_id ?? Driver::factory()->create()->id;
            $shipment->status = $status ?? 'accepted';
            $driver = Driver::find($shipment->driver_id);
            $driver->update([
                'is_available' => false,
            ]);
            $shipment->save();
        });
    }

    public function inTransit(?int $driver_id = null): static
    {
        return $this->afterCreating(function (Shipment $shipment) use ($driver_id) {
            $shipment->driver_id = $driver_id ?? Driver::factory()->create()->id;
            $driver = Driver::find($shipment->driver_id);
            $driver->update([
                'is_available' => false
            ]);
            $shipment->status = 'in_transit';
            $shipment->save();
        });
    }

    public function completed(?int $driver_id = null): static
    {
        return $this->afterCreating(function (Shipment $shipment) use ($driver_id) {
            if($shipment->driver_id === null) {
                $shipment->driver_id = $driver_id ?? Driver::factory()->create()->id;
            }
            $shipment->status = 'delivered';
            $shipment->save();
            GovernorateQueueHelper::updateGovernorateQueue($shipment->driver);

        });
    }
}
