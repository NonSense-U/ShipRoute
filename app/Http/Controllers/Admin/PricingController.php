<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditPricingMultiplierRequest;
use App\Http\Requests\EditVehiclePricingRequest;
use App\Services\PricingService;
use Illuminate\Http\Request;

class PricingController extends Controller
{

    private PricingService $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    public function getVehiclePricing()
    {
        $data = \App\Models\VehicleSizePricing::all();
        return ApiResponse::success(data: $data);
    }

    public function editVehiclePricing(EditVehiclePricingRequest $request)
    {
        $data = $this->pricingService->editVehiclePricing($request->validated());
        return ApiResponse::success(data: $data);
    }

    public function getPricingMultiplier()
    {
        $data = \App\Models\PricingMultiplier::all();
        return ApiResponse::success(data: $data);
    }

    public function editMultiplier(EditPricingMultiplierRequest $request)
    {
        $data = $this->pricingService->editPricingMultiplier($request->validated());
        return ApiResponse::success(data: $data);
    }
}
