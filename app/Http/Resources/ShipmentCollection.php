<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ShipmentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'shipments' => ShipmentResource::collection($this->collection),
            'pagination' => [
                'current_page' => $this->currentPage(),
                'per_page'     => $this->perPage(),
                'last_page'    => $this->lastPage(),
                'total'        => $this->total(),
                'has_more'     => $this->hasMorePages(),
                'next' => $this->nextPageUrl(),
                'prev' => $this->previousPageUrl(),
            ],
        ];
    }
}
