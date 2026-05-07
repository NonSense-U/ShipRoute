<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'user_id',
        'age',
        'gender',
        'current_lat',
        'current_lng',
        'vehicle_type',
        'vehicle_capacity_kg',
        'license_plate_number',
        'driver_license_number',
        'description',
    ];

    protected $casts = [
        'age' => 'integer',
        'current_lat' => 'float',
        'current_lng' => 'float',
        'vehicle_capacity_kg' => 'integer',
        'is_available' => 'boolean',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
