<?php

namespace app\Services\NewsApi;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class NewsOrg
{
    public function fetch(): array
    {
        $apiKey = config('services.newsapi.key');
        $url    = "https://newsapi.org/v2/top-headlines?country=us&pageSize=20&apiKey={$apiKey}";

        $response = Http::get($url);

        if ($response->failed()) {
            return [];
        }

        return collect($response->json('articles', []))->map(function ($item) {
            return [
                'title'        => $item['title'] ?? null,
                'description'  => $item['description'] ?? null,
                'url'          => $item['url'] ?? null,
                'url_to_image' => $item['urlToImage'] ?? null,
                'published_at' => isset($item['publishedAt']) ? Carbon::parse($item['publishedAt']) : null,
                'source_id'    => $item['source']['id'] ?? null,
                'source'       => $item['source']['name'] ?? null,
                'author'       => $item['author'] ?? null,
                'content'      => $item['content'] ?? null,
            ];
        })->all();
    }
}
