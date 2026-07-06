<?php

namespace App\Jobs;

use App\Helpers\GovernorateQueueHelper;
use App\Models\Driver;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

use function Illuminate\Log\log;

class SyncDriverGovernorate implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private int $driver_id)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $driver = Driver::findOrFail($this->driver_id);
        $response = Http::withHeaders([
            'User-Agent' => config('name') . '/' . config('version', '1.0'),
        ])->get('https://nominatim.openstreetmap.org/reverse', [
            'lat' => $driver->current_lat,
            'lon' => $driver->current_lon,
            'format' => 'jsonv2',
        ]);

        $data = $response->json();
        log('info', ['nominatim_response' => $data]);
        
        if($driver->current_governorate && $driver->current_governorate != $data['address']['state']) {
            GovernorateQueueHelper::removeDriverFromQueue($driver);
        }

        $driver->update([
            'current_governorate' => $data['address']['state']
        ]);

        GovernorateQueueHelper::updateGovernorateQueue($driver);
    }
}
