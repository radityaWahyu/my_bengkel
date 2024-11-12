<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockCorrectionResource extends JsonResource
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
            'product_name' => $this->product->name,
            'old_stock' => $this->old_stock,
            'new_stock' => $this->new_stock,
            'description' => $this->description,
            'employee' => $this->user->employee->name,
            'created_at' => $this->created_at->format('d/m/Y'),
        ];
    }
}
