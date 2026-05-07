<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\AcceptShipmentRequest;
use App\Http\Requests\StartTripRequest;
use App\Http\Requests\UpdateDriverLocationRequest;
use App\Http\Resources\ShipmentCollection;
use App\Services\DriverService;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    private DriverService $driverService;

    public function __construct(DriverService $driverService)
    {
        $this->driverService = $driverService;
    }

    public function getAvailableShipments(Request $request)
    {
        $shipments = $this->driverService->getAvailableShipments($request->user());

        return ApiResponse::success('Available shipments retrieved successfully.', new ShipmentCollection($shipments));
    }

    public function acceptShipment(AcceptShipmentRequest $request)
    {
        $shipment = $this->driverService->acceptShipment(
            $request->user(),
            $request->validated()['shipment_id']
        );

        return ApiResponse::success('Shipment accepted successfully.', $shipment);
    }

    public function startTrip(StartTripRequest $request)
    {
        $shipment = $this->driverService->startTrip(
            $request->user(),
            $request->validated()
        );

        return ApiResponse::success('Trip started successfully.', $shipment);
    }

    public function updateLocation(UpdateDriverLocationRequest $request)
    {
        $this->driverService->updateLocation($request->user(), $request->validated());

        return ApiResponse::success('Location updated successfully.');
    }
}
