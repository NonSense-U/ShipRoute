<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminService
{

    public function addDriver(array $data)
    {
        DB::beginTransaction();
        try {
            $user = User::create($data['base']);
            $user->assignRole('driver');
            $driver = $user->driver()->create($data['profile']);
            DB::commit();

            return $driver;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
