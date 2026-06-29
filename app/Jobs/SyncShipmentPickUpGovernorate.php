<?php

namespace App\Jobs;

use App\Models\ShipmentRoute;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

use function Illuminate\Log\log;

class SyncShipmentPickUpGovernorate implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private int $route_id)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $shipment_route = ShipmentRoute::findOrFail($this->route_id);
        $response = Http::withHeaders([
            'User-Agent' => config('name') . '/' . config('version', '1.0'),
        ])->get('https://nominatim.openstreetmap.org/reverse', [
            'lat' => $shipment_route->pickup_lat,
            'lon' => $shipment_route->pickup_lon,
            'format' => 'jsonv2',
        ]);

        $data = $response->json();

        $shipment_route->update([
            'pickup_governorate' => $data['address']['state']
        ]);

        $shipment = $shipment_route->shipment()->first();
        $shipment->update([
            'status' => 'scheduled'
        ]);
    }
}
