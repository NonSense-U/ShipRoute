<?php

namespace App\Services;

use App\Jobs\UploadProfilePicture;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;

class UserService
{

    public function uploadProfilePicture(User $user, UploadedFile $file)
    {
        $path = $file->store('temp');
        $profile_picture = [
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
        ];

        dispatch(new UploadProfilePicture($user, $profile_picture));
    }

    public function resetPassword(User $user, string $new_password)
    {
        $user->update([
            'password' => $new_password,
        ]);
    }
}
