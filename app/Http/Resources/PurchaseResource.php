<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
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
            'supplier' => empty($this->supplier_id) ? null : $this->supplier->name,
            'invoice_number' => $this->invoice_number,
            'purchase_code' => $this->purchase_code,
            'product_count' => $this->purchase_products_count,
            'status' => $this->status,
            'total' => $this->total,
            'extra_pay' => $this->extra_pay,
            'transaction_date' => empty($this->transaction_date) ? null : $this->transaction_date->format('d/m/Y'),
            'created_at' => $this->created_at->format('d/m/Y'),
            'employee' => $this->user->employee->name,
        ];
    }
}
