<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use RuntimeException;

class DriverService
{

	public function getAvailableShipments(User $user)
	{
		$driver = $user->driver;

		if (!$driver) {
			throw new RuntimeException('Driver profile not found.');
		}

		return Shipment::query()
			->whereIn('status', ['created', 'offered'])
			->where('vehicle_type', $driver->vehicle_type)
			->whereRaw('CAST(vehicle_capacity_kg AS DECIMAL(10,2)) <= ?', [$driver->vehicle_capacity_kg])
			->whereNull('driver_id')
			->with('merchant', 'driver')
			->paginate(20);
	}

	public function acceptShipment(User $user, int $shipmentId): Shipment
	{
		$driver = $user->driver;

		if (!$driver) {
			throw new RuntimeException('Driver profile not found.');
		}

		return DB::transaction(function () use ($driver, $shipmentId) {
			$shipment = Shipment::query()
				->where('id', $shipmentId)
				->lockForUpdate()
				->firstOrFail();

			$this->validateShipmentAcceptable($driver, $shipment);

			$shipment->update([
				'driver_id' => $driver->id,
				'status' => 'accepted',
			]);

			$driver->update(['is_available' => false]);

			return $shipment->fresh();
		});
	}

	public function updateLocation(User $user, array $payload): void
	{
		$driver = $user->driver;

		if (!$driver) {
			throw new RuntimeException('Driver profile not found.');
		}

		$driver->update([
			'current_lat' => $payload['current_lat'],
			'current_lng' => $payload['current_lng'],
			'last_location_at' => now(),
		]);
	}

	public function updateStatus(User $user, array $payload): Shipment
	{
		$driver = $user->driver;

		if (!$driver) {
			throw new RuntimeException('Driver profile not found.');
		}

		$shipment = Shipment::query()
			->where('id', $payload['shipment_id'])
			->where('driver_id', $driver->id)
			->firstOrFail();

		if ($payload['status'] === 'heading_to_pickup') {
			$shipment->update([
				'status' => 'heading_to_pickup',
			]);
		} else {
			$shipment->update([
				'status' => 'in_transit',
				'picked_up_at' => now(),
			]);
		}

		Notification::send($shipment->merchant->user, new \App\Notifications\ShipmentStatusUpdated(
			status: $shipment->status,
			shipmentId: (string) $shipment->id
		));

		return $shipment;
	}

	public function sendDeliveryOTP(User $user, array $payload): Shipment
	{
		$driver = $user->driver;

		if (!$driver) {
			throw new RuntimeException('Driver profile not found.');
		}

		$shipment = Shipment::query()
			->where('id', $payload['shipment_id'])
			->where('driver_id', $driver->id)
			->firstOrFail();

		if ($shipment->status !== 'in_transit') {
			throw new RuntimeException('Shipment must be in transit to request delivery OTP.');
		}

		// $otp = (string) random_int(100000, 999999);
		$otp = '123456';
		Cache::put("shipment_otp_{$shipment->id}", $otp, now()->addMinutes(10));

		Notification::send($shipment->merchant->user, new \App\Notifications\GamilOtp($otp));

		return $shipment;
	}

	public function completeTrip(User $user, array $payload): Shipment
	{
		$driver = $user->driver;

		if (!$driver) {
			throw new RuntimeException('Driver profile not found.');
		}

		$shipment = Shipment::query()
			->where('id', $payload['shipment_id'])
			->where('driver_id', $driver->id)
			->firstOrFail();

		if ($shipment->status !== 'in_transit') {
			throw new RuntimeException('Shipment must be in transit to complete delivery.');
		}

		$expectedOtp = (string) Cache::get("shipment_otp_{$shipment->id}");
		if ($expectedOtp === '' || $expectedOtp !== (string) $payload['otp']) {
			throw new RuntimeException('Invalid delivery OTP.');
		}

		$shipment->update([
			'status' => 'delivered',
			'delivered_at' => now(),
		]);

		$driver->update(['is_available' => true]);
		Cache::forget("shipment_otp_{$shipment->id}");

		Notification::send($shipment->merchant->user, new \App\Notifications\ShipmentStatusUpdated(
			status: $shipment->status,
			shipmentId: (string) $shipment->id
		));

		return $shipment;
	}


	public function getMyShipmentsLog(User $user)
	{
		$driver = $user->driver;

		if (!$driver) {
			throw new RuntimeException('Driver profile not found.');
		}

		return Shipment::query()
			->where('driver_id', $driver->id)
			->with('merchant', 'driver')
			->latest()
			->paginate(20);
	}

	private function validateShipmentAcceptable(Driver $driver, Shipment $shipment): void
	{
		if (!in_array($shipment->status, ['created', 'offered'], true)) {
			throw new RuntimeException('Shipment is no longer available.');
		}

		if ($shipment->driver_id) {
			throw new RuntimeException('Shipment already assigned.');
		}

		if ($driver->is_available === false) {
			throw new RuntimeException('Finish your current trip before accepting new shipments.');
		}

		if ($shipment->vehicle_type !== $driver->vehicle_type) {
			throw new RuntimeException('Vehicle type mismatch.');
		}
		if ($shipment->vehicle_capacity_kg > $driver->vehicle_capacity_kg) {
			throw new RuntimeException('Vehicle capacity insufficient.');
		}
	}
}
