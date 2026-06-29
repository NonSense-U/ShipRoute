<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\MerchantProfileCollection;
use App\Services\MerchantManagementService;
use Illuminate\Http\Request;

class MerchantManagementController extends Controller
{
    private MerchantManagementService $merchantManagementService;

    public function __construct(MerchantManagementService $merchantManagementService)
    {
        $this->merchantManagementService = $merchantManagementService;
    }

    public function index(Request $request)
    {
        $merchants = $this->merchantManagementService->listMerchants($request->query('per_page', 20));
        return ApiResponse::success(data: new MerchantProfileCollection($merchants));
    }

    public function destroy(int $merchantId)
    {
        $this->merchantManagementService->deleteMerchant($merchantId);
        return ApiResponse::success( message: 'Merchant deleted successfully');
    }
}
