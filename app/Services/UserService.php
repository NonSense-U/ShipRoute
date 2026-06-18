<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserService
{

public function resetPassword(User $user, string $new_password)
    {
        $user->update([
            'password' => $new_password,
        ]);

        Cache::delete('user_' . $user->id . '_trusted');
    }
}
