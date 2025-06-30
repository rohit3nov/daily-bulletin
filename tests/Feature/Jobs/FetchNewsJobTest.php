<?php

namespace Tests\Feature\Jobs;

use App\Jobs\FetchNews;
use App\Services\ArticleService;
use App\Contracts\NewsApiInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FetchNewsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fetches_and_stores_articles()
    {
        // Arrange: create a fake category
        $category = 'Technology';

        // Create a mock NewsApi service
        $mockApi = $this->createMock(NewsApiInterface::class);
        $mockApi->method('getName')->willReturn('mockapi');
        $mockApi->method('getRateLimit')->willReturn(10);
        $mockApi->method('fetch')->with($category)->willReturn(
            [
                [
                    'title'        => 'NASA spacecraft resumes mission',
                    'description'  => 'Psyche spacecraft resumes full thrust',
                    'content'      => 'Details of recovery...',
                    'url'          => 'https://dailygalaxy.com/2025/06/nasas-psyche-spacecraft-returns/',
                    'url_to_image' => 'https://dailygalaxy.com/image.jpg',
                    'published_at' => now()->toISOString(),
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
                    'published_at' => now()->toISOString(),
                    'source'       => 'Washington Post',
                    'source_id'    => 'wp',
                    'author'       => 'Dr. J Smith',
                ],
            ]
        );

        // Use Laravel container to auto-inject the real ArticleService
        $articleService = new ArticleService();

        // Act: Dispatch the job
        $job = new FetchNews($mockApi, $category, $articleService);
        $job->handle();

        // Assert: Category is created
        $this->assertDatabaseHas('categories', ['name' => $category]);

        // Assert: Articles are stored
        $this->assertDatabaseCount('articles', 2);
        $this->assertDatabaseHas('articles', ['title' => 'NASA spacecraft resumes mission']);
    }
}
