<?php


namespace Tests\Unit\Jobs;

use App\Jobs\FetchNews;
use App\Contracts\NewsApiInterface;
use App\Services\ArticleService;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Tests\TestCase;

class FetchNewsJobTest extends TestCase
{
    public function test_it_does_not_fetch_articles_if_rate_limited()
    {
        // Arrange
        $category = 'Science';
        $source   = 'MockApi';
        $rateKey  = "news-api-mockapi";

        $mockApi = $this->createMock(NewsApiInterface::class);
        $mockApi->method('getName')->willReturn('mockapi');
        $mockApi->method('getRateLimit')->willReturn(1);
        $mockApi->expects($this->never())->method('fetch'); // ensure fetch is NOT called

        $mockArticleService = $this->createMock(ArticleService::class);
        $mockArticleService->expects($this->never())->method('storeMany'); // no storage should happen

        // Simulate that the rate limiter is blocking this request
        RateLimiter::hit("news-api-mockapi"); // Use up the limit

        // Act
        $job = new FetchNews($mockApi, $category, $mockArticleService);
        $job->handle();

        // Assert
        $this->assertTrue(RateLimiter::tooManyAttempts("news-api-mockapi", 1));
    }

    public function test_it_fetches_articles_and_stores_them_when_not_rate_limited()
    {
        // Arrange
        $category = 'Technology';

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

        $mockArticleService = $this->createMock(ArticleService::class);
        $mockArticleService->expects($this->once())->method('storeMany')->with($articles);

        // Act
        $job = new FetchNews($mockApi, $category, $mockArticleService);
        $job->handle();

        // Assert
        // No exception means the test passed and interaction happened as expected
        $this->assertTrue(true);
    }
}
