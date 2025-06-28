<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_article_details_for_valid_id()
    {
        $article = Article::factory()->create([
              'title' => 'India’s Chandrayaan Mission',
              'source' => 'ISRO',
              'author' => 'Vikram Dev',
          ]);

        $response = $this->getJson("/api/articles/{$article->id}");

        $response->assertOk()
            ->assertJsonFragment([
             'title' => 'India’s Chandrayaan Mission',
             'source' => 'ISRO',
             'author' => 'Vikram Dev',
         ]);
    }

    /** @test */
    public function it_returns_404_for_non_existent_article()
    {
        $response = $this->getJson('/api/articles/999999');

        $response->assertNotFound();
    }
}
