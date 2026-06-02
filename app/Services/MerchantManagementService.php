<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MerchantManagementService
{
    public function listMerchants(int $perPage = 20)
    {
        return Merchant::query()->with('user')->latest()->paginate($perPage);
    }

    public function deleteMerchant(int $merchantId)
    {
        // Implement logic to delete a merchant
    }
}
