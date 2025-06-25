<?php

namespace App\Jobs;

use App\Contracts\NewsApiInterface;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\RateLimiter;
use App\Services\ArticleService;

class FetchNews implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $articles = [];

    public function __construct(
        protected NewsApiInterface $api,
        protected string $category,
        protected ArticleService $articleService
    ) {}


    public function handle(): void
    {
        $this->articles = RateLimiter::attempt(
            'news-api-' . $this->api->getName(),
            $this->api->getRateLimit(),
            function () {
                $this->api->fetch($this->category);
            }
        );

        if (!empty($this->articles)) {
            $this->articleService->storeMany($this->articles, $this->category);
        }
    }
}
