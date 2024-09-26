<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserEditResource extends JsonResource
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
            'employee_id' => $this->employee_id,
            'role' => $this->getRoleNames()[0],
        ];
    }
}
