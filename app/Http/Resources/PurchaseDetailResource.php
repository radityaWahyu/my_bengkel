<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseDetailResource extends JsonResource
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
            'supplier' => $this->whenLoaded('supplier', new SupplierResource($this->supplier), null),
            'purchase_code' => $this->purchase_code,
            'invoice_number' => $this->invoice_number,
            'status' => $this->status,
            'total' => $this->total,
            'payment_type' => empty($this->payment_id) ? null : $this->payment->name,
            'extra_pay' => $this->extra_pay,
            'purchase_products' => PurchaseProductResource::collection($this->purchase_products),
            'paid' => $this->paid,
            'transaction_date' => $this->transaction_date->format('d/m/Y'),
            'created_at' => $this->created_at->format('d/m/Y'),
            'user' => $this->whenLoaded('user', new UserResource($this->user), null)
        ];
    }
}
