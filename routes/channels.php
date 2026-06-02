<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('shipment.{shipment_id}', function (User $user, $shipment_id) {
    $shipment = \App\Models\Shipment::findOrFail($shipment_id);
    return $shipment->merchant_id === $user->merchant->id;
}, ['guards' => ['sanctum']]);