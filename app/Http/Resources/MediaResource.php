<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// API Resource (format response konsisten, termasuk whenLoaded)
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
            // whenLoaded('users')
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
