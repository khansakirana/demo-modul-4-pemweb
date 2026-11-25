<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// API Resource (format response konsisten, termasuk whenLoaded)
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            // whenLoaded('media')
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
