<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\CreateShipmentRequest;
use App\Models\Shipment;
use App\Services\MerchantService;
use Illuminate\Http\Request;
use RuntimeException;

class MerchantController extends Controller
{
    private MerchantService $merchantService;

    public function __construct(MerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    public function myShipments(Request $request)
    {
        $merchant = $request->user()->merchant;

        if (!$merchant) {
            throw new RuntimeException('Merchant profile not found.');
        }

        $shipments = Shipment::query()
            ->where('merchant_id', $merchant->id)
            ->with('route', 'driver')
            ->latest()
            ->get();

        return ApiResponse::success('Shipments retrieved successfully.', $shipments);
    }

}
