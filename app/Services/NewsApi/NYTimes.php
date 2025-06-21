<?php

namespace app\Services\NewsApi;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class NYTimes
{
    public function fetch(): array
    {
        $apiKey = config('services.nytimes.key');
        $url    = "https://api.nytimes.com/svc/topstories/v2/home.json?api-key={$apiKey}";

        $response = Http::get($url);

        if ($response->failed()) {
            return [];
        }

        return collect($response->json('results', []))->map(function ($item) {
            return [
                'title'        => $item['title'] ?? null,
                'description'  => $item['abstract'] ?? null,
                'url'          => $item['url'] ?? null,
                'url_to_image' => $item['multimedia'][0]['url'] ?? null,
                'published_at' => isset($item['published_date']) ? Carbon::parse($item['published_date']) : null,
                'source_id'    => 'nytimes',
                'source'       => 'New York Times',
                'author'       => $item['byline'] ?? null,
                'content'      => null,
            ];
        })->all();
    }
}
