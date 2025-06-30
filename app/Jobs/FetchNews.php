<?php

namespace App\Jobs;

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
        $sourceClass = "App\\Services\\NewsApi\\{$this->source}";

        if (!class_exists($sourceClass)) {
            logger()->warning("Source class {$sourceClass} not implemented. Skipping.");
            return;
        }

         $api = app($sourceClass);

         RateLimiter::attempt(
             'news-api-' . $api->getName(),
             $api->getRateLimit(),
             function () use ($api) {
                 $this->articles = $api->fetch($this->category);
             }
         );

        if (!empty($this->articles)) {
            $this->articleService->storeMany($this->articles, $this->category);
        }
    }
}
