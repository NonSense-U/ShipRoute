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
        'goods_type',
        'who_pays',
        'vehicle_type',
        'vehicle_size',
        'weight',
        'additional_details',
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

    protected $with = [
        'route',
        'merchant',
        'driver',
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
        return $this->hasOne(ShipmentRoute::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}
