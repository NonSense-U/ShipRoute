<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\CalculatePriceRequest;
use App\Http\Requests\CreateShipmentRequest;
use App\Models\Shipment;
use App\Models\ShipmentRoute;
use App\Services\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    private ShipmentService $shipmentService;

    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    public function createShipment(CreateShipmentRequest $request)
    {
        $shipment = $this->shipmentService->createShipment($request->user(), $request->validated());

        return ApiResponse::success('Shipment created successfully.', $shipment, 201);
    }

    public function getPrice(CalculatePriceRequest $request)
    {
        return ApiResponse::success(data: ['estimated_price' => $this->shipmentService->calculatePrice($request->validated())]);
    }
}
