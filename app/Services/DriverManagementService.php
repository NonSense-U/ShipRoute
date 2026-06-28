<?php

namespace App\Services;

use App\Helpers\VehicleHelper;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DriverManagementService
{

    public function listDrivers(int $perPage = 20)
    {
        return Driver::query()->with('user')->withCount('shipments')->latest()->paginate($perPage);
    }

    public function addDriver(array $data)
    {
        DB::beginTransaction();
        try {
            $user = User::create($data['base']);
            $user->assignRole('driver');
            $data['profile']['vehicle_size'] = VehicleHelper::getVehicleSize($data['profile']['vehicle_capacity_kg']);
            $driver = $user->driver()->create($data['profile']);
            DB::commit();

            return $driver;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function updateDriver(array $payload, int $driverId)
    {
        $driver = Driver::findOrFail($driverId);
        $user = $driver->user;

        DB::beginTransaction();
        try {
            if (isset($payload['base'])) {
                $user->update($payload['base']);
            }
            if (isset($payload['profile'])) {
                $driver->update($payload['profile']);
            }
            DB::commit();

            return $driver->fresh('user');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteDriver(int $driverId)
    {
        $driver = Driver::findOrFail($driverId);
        $driver->user()->delete();
    }
}
