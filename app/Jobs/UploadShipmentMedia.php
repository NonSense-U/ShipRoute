<?php

namespace App\Jobs;

use App\Helpers\UploadMediaHelper;
use App\Models\Shipment;
use Cloudinary\Cloudinary;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class UploadShipmentMedia implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    public function __construct(
        private Shipment $shipment,
        private array $files,
        private string $title,
        private ?Cloudinary $cloudinary = null
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {

            $cloudinary = $this->cloudinary ?? app(Cloudinary::class);
            $media_paths = [];
            foreach ($this->files as $file) {
                $result = UploadMediaHelper::uploadImage($cloudinary, 'shipments', $file);
                $media_paths[] = $result['secure_url'];
                unlink(storage_path('app/private/' . $file['path']));
            }

            $this->shipment->update([
                'status' => 'scheduled',
                'media' => $media_paths,
            ]);
        } catch (Throwable $e) {
            foreach ($this->files as $file) {
                unlink(storage_path('app/private/' . $file['path']));
            }
            $this->shipment->update([
                'status' => 'failed'
            ]);
            throw $e;
        }
    }
}
