<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceDetailResource extends JsonResource
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
            'vehicle' => new VehicleResource($this->vehicle),
            'status' => $this->status,
            'total' => $this->total,
            'products' => ServiceProductResource::collection($this->service_products),
            'repairs' => ServiceRepairResource::collection($this->service_repairs),
            'description' => $this->description,
            'created_at' => $this->created_at->format('d/m/Y')
        ];
    }
}
