<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleListingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_articles_with_pagination()
    {
        Article::factory()->count(30)->create();

        $response = $this->getJson('/api/articles');

        $response->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJsonCount(10, 'data');
    }

    /** @test */
    public function it_can_filter_articles_by_source_category_author_keyword_and_date()
    {
        $category = Category::factory()->create(['name' => 'Technology']);

        $article = Article::factory()->create(
            [
                'title'        => 'SpaceX Falcon Launch',
                'source'       => 'CNN',
                'author'       => 'Elon Writer',
                'published_at' => now(),
                'category_id'  => $category->id,
            ]
        );

        Article::factory()->count(10)->create(); // Noise

        $filters = [
            'source'   => 'CNN',
            'category' => 'Technology',
            'author'   => 'Elon Writer',
            'keyword'  => 'Falcon',
            'date'     => now()->toDateString(),
        ];

        $response = $this->getJson('/api/articles?' . http_build_query($filters));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'SpaceX Falcon Launch');
    }

    /** @test */
    public function it_returns_articles_with_category_data()
    {
        $category = Category::factory()->create(['name' => 'AI']);
        $article  = Article::factory()->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/articles');

        $response->assertOk()
            ->assertJsonPath('data.0.category', 'AI');
    }

    /** @test */
    public function it_responds_within_acceptable_time()
    {
        Article::factory()->count(50)->create();

        $start    = microtime(true);
        $response = $this->getJson('/api/articles');
        $elapsed  = microtime(true) - $start;

        $response->assertOk();
        $this->assertLessThan(1.5, $elapsed, 'Articles listing is too slow');
    }
}
