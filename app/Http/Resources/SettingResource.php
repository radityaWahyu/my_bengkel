<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
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
            'name' => $this->name,
            'value' => $this->type == 'image' ?
                "data:image/jpeg;base64," . base64_encode(Storage::get($this->data))
                : $this->data,
            'type' => $this->type,
            'description' => $this->description,
        ];
    }
}
