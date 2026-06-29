<?php

namespace App\Services;

use App\Models\Shipment;

class ShipmentManagement
{

    public function listShipments(int $perPage = 20)
    {
        return Shipment::query()->whereHas('merchant')->whereHas('driver')->with(['merchant', 'driver', 'route'])->latest()->paginate($perPage);
    }
}
