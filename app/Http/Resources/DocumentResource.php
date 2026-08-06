<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'type' => $this->type,
            'author' => $this->author,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ],
            'keywords' => $this->keywords ?? [],
            'status' => $this->status,
            'file_url' => $this->file_path ? asset('storage/'.$this->file_path) : null,
            'external_url' => $this->external_url,
            'views_count' => $this->views_count,
            'downloads_count' => $this->downloads_count,
            'published_at' => $this->published_at?->toDateString(),
            'created_at' => $this->created_at?->toDateString(),
            'is_favorited' => $user ? $user->favorites()->where('document_id', $this->id)->exists() : false,
            'submitted_by' => $this->whenLoaded('submitter', fn () => $this->submitter?->name),
        ];
    }
}
