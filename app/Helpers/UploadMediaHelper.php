<?php

namespace App\Helpers;
use Cloudinary\Cloudinary;

class UploadMediaHelper
{
    static public function uploadImage(Cloudinary $cloudinary, $upload_preset, $file)
    {
        $result = $cloudinary->UploadApi()->upload(storage_path('app/private/' . $file['path']), [
            'upload_preset' => $upload_preset,
            'resource_type' => 'image',
        ]);

        return $result;
    }
}
