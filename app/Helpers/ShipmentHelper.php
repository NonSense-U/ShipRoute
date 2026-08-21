<?php

namespace App\Helpers;

use App\Models\Shipment;
use App\Models\User;

class ShipmentHelper
{
    public static function getCounterParty(string $user_role, Shipment $shipment)
    {
        $counter_party['role'] = $user_role === 'merchant' ? 'driver' : 'merchant';
        $counter_party['user'] = $shipment->{$counter_party['role']}?->user;
        return $counter_party;
    }
}
