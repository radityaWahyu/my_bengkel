<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceReceiptResource extends JsonResource
{

    public static $wrap = null;
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer' => $this->vehicle->customer->name,
            'service_code' => $this->service_code,
            'plate_number' => $this->vehicle->plate_number,
            'vehicle_name' => $this->vehicle->name,
            'engine_volume' => $this->vehicle->engine_volume,
            'engine_type' => $this->vehicle->engine_type,
            'type' => $this->vehicle->type,
            'production_year' => $this->vehicle->production_year,
        ];
    }
}
