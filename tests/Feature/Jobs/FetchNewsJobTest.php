<?php

namespace Tests\Feature\Jobs;

use App\Jobs\FetchNews;
use App\Services\ArticleService;
use App\Contracts\NewsApiInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class FetchNewsJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_fetches_articles_from_api_and_stores_them()
    {
        $source   = 'MockApi';
        $category = 'Technology';
        $publishedAt = Carbon::parse('2025-06-21T12:00:00Z')->toISOString();

        $articles = [
            [
                'title'        => 'NASA spacecraft resumes mission',
                'description'  => 'Psyche spacecraft resumes full thrust',
                'content'      => 'Details of recovery...',
                'url'          => 'https://dailygalaxy.com/2025/06/nasas-psyche-spacecraft-returns/',
                'url_to_image' => 'https://dailygalaxy.com/image.jpg',
                'published_at' => $publishedAt,
                'source'       => 'The Daily Galaxy',
                'source_id'    => 'dailygalaxy',
                'author'       => null,
            ],
            [
                'title'        => 'HIV prevention shot approved',
                'description'  => 'FDA approves new shot',
                'content'      => 'Effective in trials...',
                'url'          => 'https://www.washingtonpost.com/wellness/2025/06/21/yeztugo-hiv-prevention-shot/',
                'url_to_image' => 'https://washpost.com/image.jpg',
                'published_at' => $publishedAt,
                'source'       => 'Washington Post',
                'source_id'    => 'wp',
                'author'       => 'Dr. J Smith',
            ],
        ];

        $mockApi = $this->createMock(NewsApiInterface::class);
        $mockApi->method('getName')->willReturn('mockapi');
        $mockApi->method('getRateLimit')->willReturn(10);
        $mockApi->method('fetch')->with($category)->willReturn($articles);

        App::bind(NewsApiInterface::class, function ($app, $params) use ($mockApi, $source) {
            if ($params['source'] === $source) {
                return $mockApi;
            }
            throw new \InvalidArgumentException("Unexpected source in test: {$params['source']}");
        });

        $articleService = app(ArticleService::class);

        $job = new FetchNews($source, $category, $articleService);
        $job->handle();

        $this->assertDatabaseHas('categories', ['name' => $category]);
        $this->assertDatabaseCount('articles', 2);
        $this->assertDatabaseHas('articles', ['title' => 'NASA spacecraft resumes mission']);
    }
}
