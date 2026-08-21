<?php

namespace App\Jobs;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendAvailableShipmentNotifications implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $shipmentId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $shipment = \App\Models\Shipment::find($this->shipmentId);
        if (!$shipment) {
            return;
        }
        $shipment_governorate = $shipment->route?->pickup_governorate;

        $matching_driver_ids = Driver::query()
            ->where('vehicle_type', $shipment->vehicle_type)
            ->where('vehicle_size', $shipment->vehicle_size)
            ->where('current_governorate', $shipment_governorate)
            ->where('is_available', true)
            ->pluck('user_id');

        User::query()->whereIn('id', $matching_driver_ids)
            ->chunk(100, function ($drivers) use ($shipment) {
                Notification::send($drivers, new \App\Notifications\AvailableShipmentNotification($shipment));
            });
    }
}
