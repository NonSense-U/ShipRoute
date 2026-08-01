<?php

namespace App\Services;

use App\Helpers\PricingMultiplierHelper;
use App\Helpers\VehicleHelper;
use App\Helpers\ShipmentHelper;
use App\Jobs\SyncShipmentPickUpGovernorate;
use App\Jobs\UploadShipmentMedia;
use App\Models\PricingMultiplier;
use App\Models\Shipment;
use App\Models\User;
use App\Models\VehicleSizePricing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Throwable;

class ShipmentService
{
    public function createShipment(User $user, array $payload): Shipment
    {
        $merchant = $user->merchant;

        if (!$merchant) {
            throw new RuntimeException('Merchant profile not found.');
        }

        DB::beginTransaction();

        try {
            $scheduledPickupAt = Carbon::parse($payload['scheduled_pickup_at']);

            $isNightShipping = $this->isNightShipping($scheduledPickupAt);

            $pricing = $this->calculatePrice([
                'distance' => $payload['route']['distance'],
                'weight' => $payload['weight'],
                'vehicle_type' => $payload['vehicle_type'],
                'is_night_shipping' => $isNightShipping,
            ]);

            $shipment = Shipment::create([
                'merchant_id' => $merchant->id,
                'goods_type' => $payload['goods_type'],
                'vehicle_type' => $payload['vehicle_type'],
                'vehicle_size' => VehicleHelper::getVehicleSize($payload['weight']),
                'who_pays' => $payload['who_pays'],
                'weight' => $payload['weight'],
                'additional_details' => $payload['additional_details'] ?? null,
                'is_night_shipping' => $isNightShipping,
                'scheduled_pickup_at' => $payload['scheduled_pickup_at'] ?? null,
                'price' => $pricing['total_price'],
                'status' => !empty($payload['media']) ? 'pending' : 'scheduled',
            ]);

            $this->storeShipmentMedia($shipment, $payload['media'] ?? []);

            $route = $shipment->route()->create([
                'overview_polyline' => $payload['route']['overview_polyline'],
                'pickup_lat' => $payload['route']['pickup_lat'],
                'pickup_lon' => $payload['route']['pickup_lon'],
                'delivery_lat' => $payload['route']['delivery_lat'],
                'delivery_lon' => $payload['route']['delivery_lon'],
                'distance' => $payload['route']['distance'],
                'duration_minutes' => $payload['route']['duration_minutes'],
            ]);

            $route->checkpoints()->createMany([
                [
                    'type' => 'pickup',
                    'supervisor_name' => $payload['route']['pickup_checkpoint_details']['supervisor_name'] ?? null,
                    'supervisor_phone_number' => $payload['route']['pickup_checkpoint_details']['supervisor_phone_number'] ?? null,
                    'address' => $payload['route']['pickup_checkpoint_details']['address'] ?? null,
                    'street' => $payload['route']['pickup_checkpoint_details']['street'] ?? null,
                    'building_number' => $payload['route']['pickup_checkpoint_details']['building_number'] ?? null,
                    'notes' => $payload['route']['pickup_checkpoint_details']['notes'] ?? null,
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
            DB::commit();
            dispatch(new SyncShipmentPickUpGovernorate($route->id));
            return $shipment->fresh(['route', 'merchant', 'driver']);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function calculatePrice(array $payload)
    {
        $pricing = collect();
        $vehicle_size = VehicleHelper::getVehicleSize($payload['weight']);
        $pricing_rules = VehicleSizePricing::query()
            ->where('size', $vehicle_size)
            ->firstOrFail();

        $pricing['distance_charge'] = $payload['distance'] * $pricing_rules->per_km_fee;
        $pricing['total_price'] = $pricing_rules->starting_fee + $pricing['distance_charge'];
        $pricing['starting_fee'] = $pricing_rules->starting_fee;

        if ($payload['vehicle_type'] === 'refrigerated') {
            $pricing['total_price'] += $pricing['refrigerated_surcharge'] = $pricing['total_price'] * PricingMultiplierHelper::getRefrigeratedMultiplier();
        }

        $weight_factor = ($payload['weight'] / VehicleHelper::getMaxCapacityForSize($vehicle_size)) * 100;
        $pricing['total_price'] += $pricing['weight_surcharge'] = $pricing['total_price'] * PricingMultiplierHelper::getWeightMultiplier($weight_factor);

        if ($payload['is_night_shipping'] ?? $this->isNightShipping(Carbon::parse($payload['scheduled_pickup_at']))) {
            $pricing['total_price'] += $pricing['night_shipping_surcharge'] = $pricing['total_price'] * PricingMultiplierHelper::getNightShippingMultiplier();
        }

        return $pricing;
    }

    public function cancelShipment(User $user, Shipment $shipment, string $reason = 'not specified'): void
    {
        if (!in_array($shipment->status, ['created', 'scheduled', 'accepted', 'heading_to_pickup'])) {
            throw new RuntimeException('Only unstarted shipments can be cancelled.');
        }
        $shipment->status = 'cancelled_by_' . $user_role = $user->getRoleNames()[0];
        $shipment->save();

        $counter_party = ShipmentHelper::getCounterParty($user_role, $shipment);
        if (isset($counter_party['user'])) {
            Notification::send($counter_party['user'], new \App\Notifications\ShipmentCancelledNotification($shipment, $user_role, $reason));
        }
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

    private function storeShipmentMedia(Shipment $shipment, array $mediaItems)
    {
        if (empty($mediaItems)) {
            return [];
        }

        $mediaFiles = [];
        foreach ($mediaItems as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }
            $path = $file->store('temp'); // stores in storage/app/temp

            $mediaFiles[] = [
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
            ];
        }
        dispatch(new UploadShipmentMedia($shipment, $mediaFiles, 'Shipment Media'));
    }
}
