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
        'pickup_lat',
        'pickup_lon',
        'delivery_lat',
        'delivery_lon',
        'distance',
        'duration_minutes',
    ];

    protected $casts = [
        'distance' => 'decimal:2',
        'duration_minutes' => 'integer',
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
