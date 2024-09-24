<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'name' => $this->name,
            'plate_number' => $this->plate_number,
            'machine_frame' => $this->machine_frame,
            'engine_volume' => $this->engine_volume,
            'engine_type' => $this->engine_type,
            'type' => $this->type,
            'production_year' => $this->production_year,
            'brand' => $this->brand->name,
            'customer' => $this->customer->name,
        ];
    }
}
