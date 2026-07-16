<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminDashboardAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'analytics_month',
        'active_drivers_count',
        'inactive_drivers_count',
        'active_merchants_count',
        'inactive_merchants_count',
        'shipments_per_month',
    ];

    protected $casts = [
        'analytics_month' => 'date',
        'active_drivers_count' => 'integer',
        'inactive_drivers_count' => 'integer',
        'active_merchants_count' => 'integer',
        'inactive_merchants_count' => 'integer',
        'shipments_per_month' => 'array',
    ];
}