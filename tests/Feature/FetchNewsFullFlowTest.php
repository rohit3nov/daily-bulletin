<?php

namespace Tests\Feature;

use App\Jobs\FetchNews;
use App\Services\ArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\Traits\NewsApiMockTrait;
use Tests\TestCase;

class FetchNewsFullFlowTest extends TestCase
{
    use RefreshDatabase, NewsApiMockTrait;

    /** @test */
    public function it_dispatches_jobs_and_stores_articles_and_categories_from_all_sources()
    {
        Queue::fake();

        // Use all configured news sources
        $sources = config('services.newsapi.sources', []);

        // Mock HTTP response for each source-category combination
        $this->fakeAllNewsApis();

        // Run the command
        $this->artisan('fetch:news')
            ->expectsOutput('Dispatched fetch jobs for all sources and categories.')
            ->assertExitCode(0);

        Queue::assertPushed(FetchNews::class, 22);

        // Run the jobs synchronously
        foreach (array_keys($sources) as $source) {
            $class = app()->make("App\\Services\\NewsApi\\" . ucfirst($source));
            foreach ($class->getCategories() as $category) {
                $job = new FetchNews($class, $category, app(ArticleService::class));
                $job->handle();
            }
        }

        $this->assertDatabaseCount('categories', 9);
        $this->assertDatabaseCount('articles', 15);

        $this->assertDatabaseHas('articles', [
            'title' => 'NewsAPI Article 1',
            'url'   => 'https://newsapi.org/article1',
        ]);

        $this->assertDatabaseHas('articles', [
            'title'  => 'NewsAPI Article 2',
            'url' => 'https://newsapi.org/article2',
        ]);
    }
}
