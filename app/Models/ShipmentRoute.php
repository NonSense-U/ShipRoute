<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentRoute extends Model
{
    /** @use HasFactory<\Database\Factories\ShipmentRouteFactory> */
    use HasFactory;

    protected $fillable = [
        'overview_polyline',
        'pick_up_location_details',
        'delivery_location_details',
        'pick_up_lat',
        'pick_up_lng',
        'delivery_lat',
        'delivery_lng',
        'distance',
        'duration_minutes',
    ];

    protected $casts = [
        'distance' => 'decimal:2',
        'duration_minutes' => 'integer',
        'pick_up_location_details' => 'array',
        'delivery_location_details' => 'array',
    ];

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
