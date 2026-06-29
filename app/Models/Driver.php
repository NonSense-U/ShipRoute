<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'age',
        'gender',
        'current_lat',
        'current_lon',
        'last_location_at',
        'vehicle_type',
        'vehicle_capacity_kg',
        'is_available',
        'license_plate_number',
        'driver_license_number',
        'description',
    ];

    protected $casts = [
        'age' => 'integer',
        'current_lat' => 'float',
        'current_lon' => 'float',
        'vehicle_capacity_kg' => 'float',
        'is_available' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function latestShipment()
    {
        return $this->hasOne(Shipment::class)->latestOfMany();
    }
}
