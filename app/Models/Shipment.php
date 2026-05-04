<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    /** @use HasFactory<\Database\Factories\ShipmentFactory> */
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'driver_id',
        'shipment_route_id',
        'goods_type',
        'who_pays',
        'vehicle_type',
        'vehicle_size',
        'weight',
        'requires_refrigeration',
        'is_inter_governorate',
        'is_night_shipping',
        'scheduled_pickup_at',
        'media',
        'price',
        'status',
        'picked_up_at',
        'delivered_at',
    ];

    protected $casts = [
        'requires_refrigeration' => 'boolean',
        'is_inter_governorate' => 'boolean',
        'is_night_shipping' => 'boolean',
        'weight' => 'decimal:2',
        'price' => 'decimal:2',
        'media' => 'array',
        'scheduled_pickup_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function route()
    {
        return $this->belongsTo(ShipmentRoute::class, 'shipment_route_id');
    }
}
