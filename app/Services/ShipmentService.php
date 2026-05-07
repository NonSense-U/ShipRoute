<?php

namespace App\Services;

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
                'pick_up_location_details' => $payload['route']['pick_up_location_details'] ?? null,
                'delivery_location_details' => $payload['route']['delivery_location_details'] ?? null,
                'pick_up_lat' => $payload['route']['pick_up_lat'],
                'pick_up_lng' => $payload['route']['pick_up_lng'],
                'delivery_lat' => $payload['route']['delivery_lat'],
                'delivery_lng' => $payload['route']['delivery_lng'],
                'distance' => $payload['route']['distance'],
                'duration_minutes' => $payload['route']['duration_minutes'],
            ]);

            $price = $this->calculatePrice([
                'distance' => $route->distance,
                'weight' => $payload['weight'],
                'vehicle_type' => $payload['vehicle_type'],
                // 'is_inter_governorate' => $payload['is_inter_governorate'],
                'scheduled_pickup_at' => $payload['scheduled_pickup_at'],
            ]);

            $shipment = Shipment::create([
                'merchant_id' => $merchant->id,
                'shipment_route_id' => $route->id,
                'goods_type' => $payload['goods_type'],
                'vehicle_type' => $payload['vehicle_type'],
                'vehicle_capacity_kg' => $payload['vehicle_capacity_kg'],
                'who_pays' => $payload['who_pays'],
                'weight' => $payload['weight'],
                // 'requires_refrigeration' => $payload['requires_refrigeration'],
                // 'is_inter_governorate' => $payload['is_inter_governorate'],
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

            return $shipment->refresh();
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

        // if (!empty($payload['is_inter_governorate'])) {
        //     $subtotal += $subtotal * (float) config('shipping.pricing.inter_governorate_surcharge');
        // }

        if ($this->isNightShipping(Carbon::parse($payload['scheduled_pickup_at']))) {
            $subtotal += $subtotal * (float) config('shipping.pricing.night_surcharge');
        }

        return round($subtotal, 2);
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
