<?php

namespace App\Services;

use App\Models\Article;
use app\Services\NewsApi\Guardian;
use app\Services\NewsApi\NewsOrg;
use app\Services\NewsApi\NYTimes;
use Carbon\Carbon;

class NewsFetcherService
{
    protected array $sources;

    public function __construct(array $sources = [])
    {
        $this->sources = $sources ?: [
            new NewsOrg(),
            new Guardian(),
            new NYTimes(),
        ];
    }

    public function fetchAndStore(): int
    {
        $stored = 0;

        foreach ($this->sources as $sourceFetcher) {
            $articles = $sourceFetcher->fetch();

            foreach ($articles as $article) {
                if (empty($article['url'])) {
                    continue;
                }

                $urlHash = hash('sha256', $article['url']);
                if (Article::where('url_hash', $urlHash)->exists()) {
                    continue;
                }

                Article::create(
                    [
                        'title'        => $article['title'] ?? null,
                        'description'  => $article['description'] ?? null,
                        'content'      => $article['content'] ?? null,
                        'url'          => $article['url'],
                        'url_hash'     => $urlHash,
                        'url_to_image' => $article['urlToImage'] ?? null,
                        'published_at' => Carbon::parse($article['publishedAt'] ?? now()),
                        'source'       => $article['source'] ?? null,
                        'source_id'    => $article['source_id'] ?? null,
                        'author'       => $article['author'] ?? null,
                    ]
                );

                $stored++;
            }
        }

        return $stored;
    }
}
