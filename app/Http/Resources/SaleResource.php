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
            'sale_code' => $this->sale_code,
            'product_count' => $this->sale_products_count,
            'status' => $this->status,
            'total' => $this->total,
            'total_invoice' => $this->total + $this->extra_pay,
            'payment_type' => empty($this->payment_id) ? null : $this->payment->name,
            'extra_pay' => $this->extra_pay,
            'created_at' => $this->created_at->format('d/m/Y'),
            'employee' => $this->user->employee->name,
        ];
    }
}
