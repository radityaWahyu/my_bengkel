<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceMechanicResource extends JsonResource
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
            'service_id' => $this->service_id,
            'repair_id' => $this->repair_id,
            'service_code' => $this->service_code,
            'repair_name' => $this->repair_name,
            'vehicle_plate_number' => $this->plate_number,
            'customer_name' => $this->customer_name,
            'vehicle_name' => $this->vehicle_name,
            'description' => $this->service_description,
            'started_at' => empty($this->started_at) ? null : $this->started_at->format('d/m/Y H:i:s'),
            'finished_at' => empty($this->finished_at) ? null : $this->finished_at->format('d/m/Y H:i:s'),
            'created_at' => $this->created_at->format('d/m/Y H:i:s')
        ];
    }
}
