<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\AdminService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:refresh-dashboard-analytics', function (AdminService $adminService) {
    $adminService->refreshDashboardAnalytics();

    $this->comment('Dashboard analytics refreshed.');
})->purpose('Refresh the admin dashboard analytics snapshot');

Schedule::command('admin:refresh-dashboard-analytics')->hourly();
