<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShipmentCollection;
use App\Services\ShipmentManagement;
use Illuminate\Http\Request;

class ShipmentManagementController extends Controller
{
    private ShipmentManagement $shipmentManagementService;

    public function __construct(ShipmentManagement $shipmentManagementService)
    {
        $this->shipmentManagementService = $shipmentManagementService;
    }

    public function index(Request $request)
    {
        $shipments = $this->shipmentManagementService->listShipments($request->query('per_page', 20));
        return ApiResponse::success(data: new ShipmentCollection($shipments));
    }
}
