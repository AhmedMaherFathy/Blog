<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $this->id,
            'title' => $this->getTranslation('title', $locale),
            'content' => $this->getTranslation('content', $locale),
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'author' => $this->whenLoaded('user', function () {
                return $this->user ? [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                ] : null;
            }),
            'media' => $this->whenLoaded('media') ? $this->media->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'type' => $media->mime_type,
                ];
            }) : [],
        ];
    }
}
