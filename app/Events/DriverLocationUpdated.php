<?php

namespace App\Events;

use App\Models\Driver;
use App\Models\Shipment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    protected Driver $driver;
    protected Shipment $shipment;

    /**
     * Create a new event instance.
     */
    public function __construct(int $driver_id)
    {
        $this->driver = Driver::findOrFail($driver_id);
        $this->shipment = $this->driver->currentShipment()->firstOrFail();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('shipment.' . $this->shipment->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'location.updated';
    }


    public function broadcastWith(): array
    {
        return [
            'shipment_id' => $this->shipment->id,
            'current_lat' => $this->driver->current_lat,
            'current_lng' => $this->driver->current_lng,
        ];
    }
}
