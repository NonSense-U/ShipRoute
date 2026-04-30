<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewDriverRequest;
use App\Services\AdminService;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    private AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function addDriver(NewDriverRequest $request)
    {
        $driver = $this->adminService->addDriver($request->validated());
        return ApiResponse::success('Driver added successfully', $driver, 201);
    }
}
