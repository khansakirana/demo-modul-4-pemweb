<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// [PENANDA API RESOURCE MEDIA] Konsistensi format JSON + whenLoaded('users')
class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'genre' => $this->genre,
            'year' => $this->year,
            'episodes' => $this->episodes,
            'country' => $this->country,
            'details' => $this->details,
            // [PENANDA whenLoaded('users')] Hanya tampil jika relasi users sudah di-load
            'users' => $this->whenLoaded('users', function () {
                return $this->users->map(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]);
            }),
        ];
    }
}
