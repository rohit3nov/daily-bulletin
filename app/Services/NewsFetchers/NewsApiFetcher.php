<?php

namespace App\Services\NewsFetchers;

use Illuminate\Support\Facades\Http;
use App\Models\Article;
use Illuminate\Support\Carbon;

class NewsApiFetcher
{
    protected string $apiUrl = 'https://newsapi.org/v2/top-headlines';
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.newsapi.key');
    }

    public function fetchAndStore(): void
    {
        $response = Http::get($this->apiUrl, [
            'country'  => 'us',
            'pageSize' => 20,
            'apiKey'   => $this->apiKey,
        ]);

        if ($response->failed()) {
            throw new \Exception("NewsAPI fetch failed");
        }

        $articles = $response->json()['articles'];

        foreach ($articles as $data) {

            $url = $data['url'] ?? null;

            if (!$url) continue;

            $urlHash = hash('sha256', $url);

            // Skip if already exists
            if (Article::where('url_hash', $urlHash)->exists()) {
                continue;
            }

            Article::create([
                'title'        => $data['title'],
                'description'  => $data['description'],
                'content'      => $data['content'],
                'url'          => $url,
                'url_hash'     => $urlHash,
                'url_to_image' => $data['urlToImage'],
                'published_at' => Carbon::parse($data['publishedAt']),
                'source'       => $data['source']['name'] ?? null,
                'source_id'    => $data['source']['id'] ?? null,
                'author'       => $data['author'],
            ]);
        }
    }
}
