<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleDetailResource extends JsonResource
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
            'sale_code' => $this->sale_code,
            'status' => $this->status,
            'total' => $this->total,
            'payment_type' => empty($this->payment_id) ? null : $this->payment->name,
            'extra_pay' => $this->extra_pay,
            'products' => SaleProductResource::collection($this->sale_products),
            'paid' => $this->paid,
            'created_at' => $this->created_at->format('d/m/Y')
        ];
    }
}
