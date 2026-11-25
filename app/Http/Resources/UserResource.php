<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// [PENANDA API RESOURCE USER] Konsistensi format JSON + whenLoaded('media')
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            // [PENANDA whenLoaded('media')] Hanya tampil jika relasi media sudah di-load
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
