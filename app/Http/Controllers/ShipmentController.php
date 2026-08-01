<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\CalculatePriceRequest;
use App\Http\Requests\CreateShipmentRequest;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    use AuthorizesRequests;

    private ShipmentService $shipmentService;

    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    public function createShipment(CreateShipmentRequest $request)
    {
        $shipment = $this->shipmentService->createShipment($request->user(), $request->validated());

        return ApiResponse::success('Shipment created successfully.', new ShipmentResource($shipment), 201);
    }

    public function getPrice(CalculatePriceRequest $request)
    {
        return ApiResponse::success(data: $this->shipmentService->calculatePrice($request->validated()));
    }

    public function cancelShipment(Request $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('update', $shipment);

        $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);
        $this->shipmentService->cancelShipment($request->user(), $shipment, $request->input('reason'));

        return ApiResponse::success('Shipment cancelled successfully.');
    }
}
