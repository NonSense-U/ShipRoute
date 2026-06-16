<?php

namespace App\Helpers;


class VehicleHelper
{

    public static function getVehicleSize(float $vehicle_capacity_kg)
    {
        if ($vehicle_capacity_kg <= 900) {
            return 'small';
        } elseif ($vehicle_capacity_kg <= 2000) {
            return 'medium';
        } elseif ($vehicle_capacity_kg <= 4000) {
            return 'large';
        } else {
            throw new \InvalidArgumentException('Vehicle capacity exceeds the maximum limit of 4000 kg.');
        }
    }
}
