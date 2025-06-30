<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $this->description,
            'content'       => $this->content,
            'author'        => $this->author,
            'source'        => $this->source,
            'source_id'     => $this->source_id,
            'url'           => $this->url,
            'url_to_image'  => $this->url_to_image,
            'published_at'  => \Carbon\Carbon::parse($this->published_at)->toIso8601String(),
            'category'      => $this->category?->name, // assumes `category` is eager-loaded
        ];
    }
}
