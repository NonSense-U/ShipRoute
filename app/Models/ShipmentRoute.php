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
        'pick_up_lon',
        'delivery_lat',
        'delivery_lon',
        'distance',
        'duration_minutes',
    ];

    protected $casts = [
        'distance' => 'decimal:2',
        'duration_minutes' => 'integer',
        'pick_up_location_details' => 'array',
        'delivery_location_details' => 'array',
    ];

    protected $with = ['checkpoints'];

    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class);
    }
}
