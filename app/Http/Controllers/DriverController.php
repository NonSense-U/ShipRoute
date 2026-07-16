<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\AcceptShipmentRequest;
use App\Http\Requests\CompleteTripRequest;
use App\Http\Requests\SendDeliveryOtpRequest;
use App\Http\Requests\UpdateShipmentStatusRequest;
use App\Http\Requests\UpdateDriverLocationRequest;
use App\Http\Resources\ProfitCollection;
use App\Http\Resources\ShipmentCollection;
use App\Http\Resources\ShipmentResource;
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

        return ApiResponse::success('Shipment accepted successfully.', new ShipmentResource($shipment));
    }

    public function updateStatus(UpdateShipmentStatusRequest $request)
    {
        $shipment = $this->driverService->updateStatus(
            $request->user(),
            $request->validated()
        );

        return ApiResponse::success('Shipment status updated successfully.', new ShipmentResource($shipment));
    }

    public function sendDeliveryOTP(Request $request)
    {
        $shipment = $this->driverService->sendDeliveryOTP($request->user());

        return ApiResponse::success('Delivery OTP sent to merchant.', new ShipmentResource($shipment));
    }

    public function completeTrip(CompleteTripRequest $request)
    {
        $shipment = $this->driverService->completeTrip(
            $request->user(),
            $request->validated()
        );

        return ApiResponse::success('Trip completed successfully.', new ShipmentResource($shipment));
    }

    public function updateLocation(UpdateDriverLocationRequest $request)
    {
        $this->driverService->updateLocation($request->user(), $request->validated());

        return ApiResponse::success('Location updated successfully.');
    }

    public function myShipmentLogs(Request $request)
    {
        $shipments = $this->driverService->getMyShipmentsLog($request->user(), $request->query('status'));

        return ApiResponse::success('My shipments retrieved successfully.', new ShipmentCollection($shipments));
    }

    public function myProfits(Request $request)
    {
        $shipments = $this->driverService->myProfits($request->user()->driver);

        return ApiResponse::success('My profits retrieved successfully.', new ProfitCollection($shipments));
    }
}
