<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JurnalEditResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {


        return [
            'id' => $this->id,
            'income' => (int)$this->income,
            'expense' => (int)$this->expense,
            'transaction_date' => $this->transaction_date->format('Y-m-d'),
            'payment' => $this->payment_id,
            'description' => $this->description,
        ];
    }
}
