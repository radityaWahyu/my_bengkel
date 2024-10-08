<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'service_code' => $this->service_code,
            'vehicle_plate_number' => $this->vehicle->plate_number,
            'vehicle_name' => $this->vehicle->name,
            'customer_name' => $this->vehicle->customer->name,
            'status' => $this->status,
            'total' => $this->total,
            'extra_pay' => $this->extra_pay,
            'created_at' => $this->created_at->format('d/m/Y')
        ];
    }
}
