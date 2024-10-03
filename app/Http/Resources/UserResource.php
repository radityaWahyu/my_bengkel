<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'username' => $this->username,
            'name' => $this->employee->name,
            'employee_id' => $this->employee->id,
            'gender' => $this->employee->gender,
            'phone' => $this->employee->phone,
            'whatsapp' => $this->employee->whatsapp,
            'role' => $this->getRoleNames()[0],
            'enabled' => $this->is_enabled
        ];
    }
}
