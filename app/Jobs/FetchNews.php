<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\RateLimiter;
use App\Services\ArticleService;
use App\Contracts\NewsApiInterface;

class FetchNews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $source,
        protected string $category,
        protected ArticleService $articleService
    ) {
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function handle(): void
    {
        $api = app()->makeWith(NewsApiInterface::class, ['source' => $this->source]);

        if (RateLimiter::tooManyAttempts('news-api-' . $api->getName(), $api->getRateLimit())) {
            return;
        }

        $articles = $api->fetch($this->category);

        if (!empty($articles)) {
            $this->articleService->storeMany($articles, $this->category);
        }
    }
}
