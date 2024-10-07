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
            'started_at' => empty($this->started_at) ? null : $this->started_at->format('d/m/Y H:i:s'),
            'finished_at' => empty($this->finished_at) ? null : $this->finished_at->format('d/m/Y H:i:s')
        ];
    }
}
