<?php

namespace Tests\Unit\Jobs;

use App\Jobs\FetchNews;
use App\Contracts\NewsApiInterface;
use App\Services\ArticleService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class FetchNewsJobTest extends TestCase
{
    public function test_it_does_not_fetch_articles_if_rate_limited()
    {
        $category = 'Science';
        $source   = 'mockapi';

        $mockApi = $this->createMock(NewsApiInterface::class);
        $mockApi->method('getName')->willReturn('mockapi');
        $mockApi->method('getRateLimit')->willReturn(1);
        $mockApi->expects($this->never())->method('fetch');

        App::bind(NewsApiInterface::class, fn() => $mockApi);

        $mockArticleService = $this->createMock(ArticleService::class);
        $mockArticleService->expects($this->never())->method('storeMany');

        RateLimiter::hit("news-api-mockapi");

        $job = new FetchNews($source, $category, $mockArticleService);
        $job->handle();

        $this->assertTrue(RateLimiter::tooManyAttempts("news-api-mockapi", 1));
    }

    public function test_it_fetches_articles_and_stores_them_when_not_rate_limited()
    {
        $category = 'Technology';
        $source   = 'mockapi';

        $articles = [
            [
                'title' => 'Sample Title',
                'url'   => 'https://example.com/news1',
            ]
        ];

        $mockApi = $this->createMock(NewsApiInterface::class);
        $mockApi->method('getName')->willReturn('mockapi');
        $mockApi->method('getRateLimit')->willReturn(5);
        $mockApi->expects($this->once())->method('fetch')->with($category)->willReturn($articles);

        App::bind(NewsApiInterface::class, fn() => $mockApi);

        $mockArticleService = $this->createMock(ArticleService::class);
        $mockArticleService->expects($this->once())->method('storeMany')->with($articles, $category);

        $job = new FetchNews($source, $category, $mockArticleService);
        $job->handle();

        $this->assertTrue(true);
    }
}
