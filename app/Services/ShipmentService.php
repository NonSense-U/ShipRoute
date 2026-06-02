<?php

namespace App\Services;

use App\Models\Checkpoint;
use App\Models\Shipment;
use App\Models\ShipmentRoute;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class ShipmentService
{
    public function createShipment(User $user, array $payload): Shipment
    {
        $merchant = $user->merchant;

        if (!$merchant) {
            throw new RuntimeException('Merchant profile not found.');
        }

        return DB::transaction(function () use ($merchant, $payload) {
            $scheduledPickupAt = isset($payload['scheduled_pickup_at'])
                ? Carbon::parse($payload['scheduled_pickup_at'])
                : now();
            $isNightShipping = $this->isNightShipping($scheduledPickupAt);

            $route = ShipmentRoute::create([
                'overview_polyline' => $payload['route']['overview_polyline'],
                'pick_up_lat' => $payload['route']['pick_up_lat'],
                'pick_up_lng' => $payload['route']['pick_up_lng'],
                'delivery_lat' => $payload['route']['delivery_lat'],
                'delivery_lng' => $payload['route']['delivery_lng'],
                'distance' => $payload['route']['distance'],
                'duration_minutes' => $payload['route']['duration_minutes'],
            ]);

            $route->checkpoints()->createMany([
                [
                    'type' => 'pick_up',
                    'supervisor_name' => $payload['route']['pick_up_checkpoint_details']['supervisor_name'] ?? null,
                    'supervisor_phone_number' => $payload['route']['pick_up_checkpoint_details']['supervisor_phone_number'] ?? null,
                    'address' => $payload['route']['pick_up_checkpoint_details']['address'] ?? null,
                    'street' => $payload['route']['pick_up_checkpoint_details']['street'] ?? null,
                    'building_number' => $payload['route']['pick_up_checkpoint_details']['building_number'] ?? null,
                    'notes' => $payload['route']['pick_up_checkpoint_details']['notes'] ?? null,
                ],
                [
                    'type' => 'delivery',
                    'supervisor_name' => $payload['route']['delivery_checkpoint_details']['supervisor_name'] ?? null,
                    'supervisor_phone_number' => $payload['route']['delivery_checkpoint_details']['supervisor_phone_number'] ?? null,
                    'address' => $payload['route']['delivery_checkpoint_details']['address'] ?? null,
                    'street' => $payload['route']['delivery_checkpoint_details']['street'] ?? null,
                    'building_number' => $payload['route']['delivery_checkpoint_details']['building_number'] ?? null,
                    'notes' => $payload['route']['delivery_checkpoint_details']['notes'] ?? null,
                ],
            ]);

            $price = $this->calculatePrice([
                'distance' => $route->distance,
                'weight' => $payload['weight'],
                'vehicle_type' => $payload['vehicle_type'],
                'is_night_shipping' => $isNightShipping,
            ]);

            $shipment = Shipment::create([
                'merchant_id' => $merchant->id,
                'shipment_route_id' => $route->id,
                'goods_type' => $payload['goods_type'],
                'vehicle_type' => $payload['vehicle_type'],
                'vehicle_capacity_kg' => $payload['vehicle_capacity_kg'],
                'who_pays' => $payload['who_pays'],
                'weight' => $payload['weight'],
                'additional_details' => $payload['additional_details'] ?? null,
                'is_night_shipping' => $isNightShipping,
                'scheduled_pickup_at' => $payload['scheduled_pickup_at'] ?? null,
                'price' => $price,
                'status' => 'created',
            ]);

            $mediaPaths = $this->storeShipmentMedia($shipment, $payload['media'] ?? []);
            if (!empty($mediaPaths)) {
                $shipment->media = $mediaPaths;
                $shipment->save();
            }

            return $shipment->fresh(['route', 'merchant', 'driver']);
        });
    }

    public function calculatePrice(array $payload): float
    {
        $baseFee = (float) config('shipping.pricing.base_fee');
        $perKm = (float) config('shipping.pricing.per_km');
        $perKg = (float) config('shipping.pricing.per_kg');

        $distanceCharge = $payload['distance'] * $perKm;
        $weightCharge = $payload['weight'] * $perKg;

        $subtotal = $baseFee + $distanceCharge + $weightCharge;

        if (!empty($payload['vehicle_type']) && $payload['vehicle_type'] === 'refrigerated') {
            $subtotal += $subtotal * (float) config('shipping.pricing.refrigeration_surcharge');
        }

        if ($payload['is_night_shipping']) {
            $subtotal += $subtotal * (float) config('shipping.pricing.night_surcharge');
        }

        return round($subtotal, 2);
    }

    public function cancelShipment(Shipment $shipment): void
    {
        if (!in_array($shipment->status, ['created', 'scheduled'])) {
            throw new RuntimeException('Only shipments in created or scheduled status can be cancelled.');
        }

        $shipment->status = 'cancelled';
        $shipment->save();
    }

    private function isNightShipping(Carbon $scheduledPickupAt): bool
    {
        $startHour = (int) config('shipping.night_hours.start', 20);
        $endHour = (int) config('shipping.night_hours.end', 6);
        $hour = (int) $scheduledPickupAt->format('G');

        if ($startHour < $endHour) {
            return $hour >= $startHour && $hour < $endHour;
        }

        return $hour >= $startHour || $hour < $endHour;
    }

    private function storeShipmentMedia(Shipment $shipment, array $mediaItems): array
    {
        if (empty($mediaItems)) {
            return [];
        }

        $paths = [];
        foreach ($mediaItems as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $paths[] = $file->store("shipments/{$shipment->id}", 'public');
        }

        return $paths;
    }
}
