<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
    }

    public function updateSensitiveInfo(User $user): bool
    {
        return Cache::get('user_' . $user->id . '_trusted', false);
    }
}
