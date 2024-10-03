<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRepairResource extends JsonResource
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
            'name' => $this->repair->name,
            'employee_id' => $this->whenLoaded('employee', $this->employee_id, null),
            'employee_name' => empty($this->employee_id) ? null : $this->employee->name,
            'qty' => $this->qty,
            'price' => $this->price,
            'total' => $this->total,
        ];
    }
}
