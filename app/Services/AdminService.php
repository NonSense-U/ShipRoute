<?php

namespace App\Services;

use App\Models\AdminDashboardAnalytics;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminService
{

    public function getDashboardAnalytics(): array
    {
        $snapshot = AdminDashboardAnalytics::query()
            ->whereNotNull('analytics_month')
            ->latest('analytics_month')
            ->first();

        if (!$snapshot) {
            $snapshot = $this->refreshDashboardAnalytics();
        }

        $previousSnapshot = AdminDashboardAnalytics::query()
            ->whereNotNull('analytics_month')
            ->where('analytics_month', '<', $snapshot->analytics_month)
            ->latest('analytics_month')
            ->first();

        return [
            'drivers' => [
                'active_drivers' => [
                    'count' => $snapshot->active_drivers_count,
                    'change_ratio' => $previousSnapshot ? ($snapshot->active_drivers_count - $previousSnapshot->active_drivers_count) / $previousSnapshot->active_drivers_count : null
                ],
                'inactive_drivers' => [
                    'count' => $snapshot->inactive_drivers_count,
                    'change_ratio' => $previousSnapshot ? ($snapshot->inactive_drivers_count - $previousSnapshot->inactive_drivers_count) / $previousSnapshot->inactive_drivers_count : null
                ],
            ],
            'merchants' => [
                'active_merchants' => [
                    'count' => $snapshot->active_merchants_count,
                    'change_ratio' => $previousSnapshot ? ($snapshot->active_merchants_count - $previousSnapshot->active_merchants_count) / $previousSnapshot->active_merchants_count : null
                ],
                'inactive_merchants' => [
                    'count' => $snapshot->inactive_merchants_count,
                    'change_ratio' => $previousSnapshot ? ($snapshot->inactive_merchants_count - $previousSnapshot->inactive_merchants_count) / $previousSnapshot->inactive_merchants_count : null
                ],
            ],
            'shipments_per_month' => $snapshot->shipments_per_month ?? [],
        ];
    }

    public function refreshDashboardAnalytics(): AdminDashboardAnalytics
    {
        $driverName = DB::connection()->getDriverName();
        $analyticsMonth = now()->startOfMonth()->toDateString();

        $monthExpression = match ($driverName) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        $shipmentsPerMonth = Shipment::withoutGlobalScopes()
            ->selectRaw("{$monthExpression} as month, COUNT(*) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($row) => [
                'month' => $row->month,
                'count' => (int) $row->total,
            ])
            ->values()
            ->all();

        return AdminDashboardAnalytics::query()->updateOrCreate(
            ['analytics_month' => $analyticsMonth],
            [
                'analytics_month' => $analyticsMonth,
                'active_drivers_count' => User::query()->role('driver')->where('last_login_at', '>=', now()->subMonth())->count(),
                'inactive_drivers_count' => User::query()->role('driver')->where(function ($query) {
                    $query->whereNull('last_login_at')->orWhere('last_login_at', '<', now()->subMonth());
                })->count(),
                'active_merchants_count' => User::query()->role('merchant')->where('last_login_at', '>=', now()->subMonth())->count(),
                'inactive_merchants_count' => User::query()->role('merchant')->where(function ($query) {
                    $query->whereNull('last_login_at')->orWhere('last_login_at', '<', now()->subMonth());
                })->count(),
                'shipments_per_month' => $shipmentsPerMonth,
            ]
        );
    }
}
