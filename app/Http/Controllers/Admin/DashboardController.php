<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\AdminService;

class DashboardController extends Controller
{
    public function __construct(private readonly AdminService $adminService)
    {
    }

    public function analytics()
    {
        return ApiResponse::success('Dashboard analytics retrieved successfully', $this->adminService->getDashboardAnalytics());
    }
}