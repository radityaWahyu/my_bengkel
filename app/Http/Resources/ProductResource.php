<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'sale_price' => $this->sale_price,
            'buy_price' => $this->buy_price,
            'stock' => $this->stock,
            'category' => $this->category->name,
            'rack' => $this->rack->name,
            'unit' => $this->unit->name ?? '',
        ];
    }
}
