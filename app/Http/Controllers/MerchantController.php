<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Services\MerchantService;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    private MerchantService $merchantService;

    public function __construct(MerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

}
