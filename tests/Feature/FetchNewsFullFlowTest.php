<?php

namespace Tests\Feature;

use App\Jobs\FetchNews;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class FetchNewsFullFlowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_dispatches_jobs_and_stores_articles_and_categories_from_all_sources()
    {
        Queue::fake();

        // Limit to 4 categories from config
        $categories = array_slice(config('services.newsapi.categories', []), 0, 4);

        // Use all configured sources
        $sources = config('services.newsapi.sources', []);

        // Mock HTTP response for each source-category combination
        foreach ($sources as $sourceKey => $sourceConfig) {
            foreach ($categories as $category) {
                Http::fake(
                    [
                        $sourceConfig['url'] . '*' => Http::response(
                            [
                                'status'       => 'ok',
                                'totalResults' => 5,
                                'articles'     => [
                                    [
                                        'source'      => [
                                            'id'   => null,
                                            'name' => 'BBC News'
                                        ],
                                        'author'      => null,
                                        'title'       => 'Minnesota shootings suspect was a prepper',
                                        'description' => 'After shooting two local politicians...',
                                        'url'         => 'https://www.bbc.com/news/articles/c89ev9j955ko',
                                        'urlToImage'  => 'https://ichef.bbci.co.uk/news/1024/branded_news/sample1.jpg',
                                        'publishedAt' => now()->subHour()->toIso8601String(),
                                        'content'     => 'Sample content 1'
                                    ],
                                    [
                                        'source'      => [
                                            'id'   => 'fortune',
                                            'name' => 'Fortune'
                                        ],
                                        'author'      => 'Jason Ma',
                                        'title'       => 'Top economist sees Trump victory',
                                        'description' => 'This would seem like a victory...',
                                        'url'         => 'https://fortune.com/2025/06/21/trump-tariffs',
                                        'urlToImage'  => 'https://fortune.com/sample2.jpg',
                                        'publishedAt' => now()->subHours(
                                            2
                                        )->toIso8601String(),
                                        'content'     => 'Sample content 2'
                                    ],
                                    [
                                        'source'      => [
                                            'id'   => 'guardian',
                                            'name' => 'The Guardian'
                                        ],
                                        'author'      => 'Staff Writer',
                                        'title'       => 'NASA Psyche returns to full thrust',
                                        'description' => 'NASA\'s Psyche spacecraft is back...',
                                        'url'         => 'https://guardian.com/space/psyche-returns',
                                        'urlToImage'  => 'https://guardian.com/images/sample3.jpg',
                                        'publishedAt' => now()->subHours(
                                            3
                                        )->toIso8601String(),
                                        'content'     => 'Sample content 3'
                                    ],
                                    [
                                        'source'      => [
                                            'id'   => 'npr',
                                            'name' => 'NPR'
                                        ],
                                        'author'      => 'AP',
                                        'title'       => 'Gaza aid continues in crisis',
                                        'description' => 'Aid trickles in amid conflict...',
                                        'url'         => 'https://npr.org/2025/06/21/gaza-aid',
                                        'urlToImage'  => 'https://npr.org/images/sample4.jpg',
                                        'publishedAt' => now()->subHours(
                                            4
                                        )->toIso8601String(),
                                        'content'     => 'Sample content 4'
                                    ],
                                    [
                                        'source'      => [
                                            'id'   => 'ap',
                                            'name' => 'Associated Press'
                                        ],
                                        'author'      => 'John Doe',
                                        'title'       => 'Court blocks law in Louisiana',
                                        'description' => 'Ten Commandments law struck down...',
                                        'url'         => 'https://apnews.com/article/law-blocked',
                                        'urlToImage'  => 'https://apnews.com/images/sample5.jpg',
                                        'publishedAt' => now()->subHours(
                                            5
                                        )->toIso8601String(),
                                        'content'     => 'Sample content 5'
                                    ]
                                ]
                            ],
                            200
                        )
                    ]
                );
            }
        }

        // Run the command
        $this->artisan('fetch:news')
            ->expectsOutput('Dispatched fetch jobs for all sources and categories.')
            ->assertExitCode(0);

        Queue::assertPushed(FetchNews::class, $times = count($sources) * count($categories));

        // Run the jobs synchronously
        foreach (array_keys($sources) as $source) {
            $class = app()->make("App\\Services\\NewsApi\\" . ucfirst($source));
            foreach ($categories as $category) {
                FetchNews::dispatchSync($class, $category);
            }
        }

        // Assertions
        $this->assertDatabaseCount('categories', 4);
        $this->assertDatabaseCount('articles', 4 * count($sources) * 5); // 4 categories × 3 sources × 5 articles = 60

        // Additional category name assertions
        foreach ($categories as $category) {
            $this->assertDatabaseHas('categories', ['name' => $category]);
        }

        // Additional article assertions
        $this->assertDatabaseHas('articles', [
            'title' => 'Minnesota shootings suspect was a prepper',
            'url'   => 'https://www.bbc.com/news/articles/c89ev9j955ko',
        ]);

        $this->assertDatabaseHas('articles', [
            'title'  => 'Top economist sees Trump victory',
            'author' => 'Jason Ma',
        ]);

        $this->assertDatabaseHas('articles', [
            'title'  => 'NASA Psyche returns to full thrust',
            'source' => 'The Guardian',
        ]);

        // Assert article is linked to category
        $category = Category::where('name', $categories[0])->first();
        $this->assertTrue($category->articles()->count() > 0);

        $article = Article::where('title', 'Minnesota shootings suspect was a prepper')->first();
        $this->assertEquals($category->id, $article->category_id);
    }
}
