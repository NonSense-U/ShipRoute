<?php

namespace App\Helpers;

use App\Models\Driver;
use Illuminate\Support\Facades\Cache;

class GovernorateQueueHelper
{
    public static function getGovernorateQueueIndex(Driver $driver): int
    {
        $governorate_queue = self::updateGovernorateQueue($driver);

        $index = collect($governorate_queue)
            ->search(fn($item) => $item['driver_id'] === $driver->id);
        return $index;
    }

    public static function updateGovernorateQueue(Driver $driver): array
    {
        $governorate_queue = Cache::get($driver->current_governorate . ' queue', []);

        $key = collect($governorate_queue)
            ->search(fn($item) => $item['driver_id'] === $driver->id);

        if ($key !== false) {
            $governorate_queue[$key]['latest_shipment_at'] = $driver->latest_shipment_at;
        } else {
            $governorate_queue[] = [
                'driver_id' => $driver->id,
                'latest_shipment_at' => $driver->latest_shipment_at,
            ];
        }

        $governorate_queue = self::sortGovernorateQueue($governorate_queue);

        Cache::put($driver->current_governorate . ' queue', $governorate_queue, now()->addHours(6));

        return $governorate_queue;
    }

    public static function removeDriverFromQueue(Driver $driver): void
    {
        $governorate_queue = Cache::get($driver->current_governorate . ' queue', []);

        $governorate_queue = collect($governorate_queue)
            ->reject(fn($item) => $item['driver_id'] === $driver->id)
            ->values()
            ->toArray();

        Cache::put($driver->current_governorate . ' queue', $governorate_queue, now()->addHours(6));
    }

    public static function sortGovernorateQueue(array $governorate_queue): array
    {
        $sorted =  collect($governorate_queue)
            ->sortBy(fn($item) => $item['latest_shipment_at'] ?? '')
            ->values()
            ->all();
        return $sorted;
    }
}
