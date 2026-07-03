<?php

namespace App\Jobs;

use App\Helpers\UploadMediaHelper;
use App\Models\User;
use Cloudinary\Cloudinary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\UploadedFile;
use Throwable;

class UploadProfilePicture implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public User $user, public array $file, public ?Cloudinary $cloudinary = null)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $cloudinary = $this->cloudinary ?? app(Cloudinary::class);
            $result = UploadMediaHelper::uploadImage($cloudinary, 'shipments', $this->file);
            $this->user->update(['profile_picture_url' => $result['secure_url']]);
            unlink(storage_path('app/private/' . $this->file['path']));
        } catch (Throwable $e) {
            unlink(storage_path('app/private/' . $this->file['path']));
            throw $e;
        }
    }
}
