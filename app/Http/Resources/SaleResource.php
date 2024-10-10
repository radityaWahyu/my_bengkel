<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'sale_code' => $this->service_code,
            'product_count' => $this->sale_product_count,
            'status' => $this->status,
            'total' => $this->total,
            'extra_pay' => $this->extra_pay,
            'created_at' => $this->created_at->format('d/m/Y')
        ];
    }
}
