<?php

namespace App\Observers;

use App\Services\AdminService;

class DashboardAnalyticsObserver
{
    public function saved(): void
    {
        app(AdminService::class)->refreshDashboardAnalytics();
    }

    public function deleted(): void
    {
        app(AdminService::class)->refreshDashboardAnalytics();
    }
}