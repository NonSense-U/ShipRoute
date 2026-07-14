<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Http\Resources\DriverProfileCollection;
use App\Http\Resources\DriverProfileResource;
use App\Http\Resources\ProfitCollection;
use App\Services\DriverManagementService;
use Illuminate\Http\Request;

class DriverManagementController extends Controller
{
    private DriverManagementService $driverManagementService;

    public function __construct(DriverManagementService $driverManagementService)
    {
        $this->driverManagementService = $driverManagementService;
    }

    public function index(Request $request)
    {
        $drivers = $this->driverManagementService->listDrivers($request->input('perPage', 20));
        return ApiResponse::success('Drivers retrieved successfully', new DriverProfileCollection($drivers));
    }

    public function store(NewDriverRequest $request)
    {
        $driver = $this->driverManagementService->addDriver($request->validated());
        return ApiResponse::success('Driver added successfully', new DriverProfileResource($driver), 201);
    }

    public function update(UpdateDriverRequest $request, int $driverId)
    {
        $updatedDriver = $this->driverManagementService->updateDriver($request->validated(), $driverId);
        return ApiResponse::success('Driver updated successfully', new DriverProfileResource($updatedDriver));
    }

    public function destroy(Request $request, int $driverId)
    {
        $this->driverManagementService->deleteDriver($driverId);
        return ApiResponse::success('Driver deleted successfully');
    }

    public function getDriverProfits(Request $request, int $driverId)
    {
        $unprocessedProfits = $this->driverManagementService->getDriverProfits($driverId, $request->input('perPage', 20), $request->input('processed', false));
        return ApiResponse::success('Unprocessed profits retrieved successfully', [
            'total_profits' => $unprocessedProfits['total_price'],
            'owed_amount' => $unprocessedProfits['owed_amount'],
            'records' => new ProfitCollection($unprocessedProfits['shipments'])
        ]);
    }

    // public function processDriverProfits(Request $request, int $driverId)
    // {
    //     $this->driverManagementService->processDriverProfits($driverId);
    //     return ApiResponse::success('Driver profits processed successfully');
    // }
}
