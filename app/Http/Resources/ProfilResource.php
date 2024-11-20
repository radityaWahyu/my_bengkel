<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfilResource extends JsonResource
{
    public static $wrap = null;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->id,
            'employee_id' => $this->employee->id,
            'name' => $this->employee->name,
            'phone' =>  $this->employee->phone,
            'whatsapp' => $this->employee->whatsapp,
            'address' => $this->employee->address,
            'gender' => $this->employee->gender,
            'username' => $this->username,
        ];
    }
}
