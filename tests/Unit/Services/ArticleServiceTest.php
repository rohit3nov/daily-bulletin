<?php

namespace Tests\Unit\Services;

use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ArticleService $articleService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->articleService = new ArticleService();
    }

    /** @test */
    public function it_creates_a_new_article_if_not_exists()
    {
        $data = [
            'title'        => 'Sample Article',
            'description'  => 'This is a test article.',
            'url'          => 'https://example.com/article-1',
            'url_to_image' => 'https://example.com/image.jpg',
            'published_at' => now()->toDateTimeString(),
            'source'       => 'Test Source',
            'source_id'    => 'test',
            'author'       => 'Test Author',
            'content'      => 'Some content here',
        ];

        $this->articleService->storeMany([$data], "Sample");

        $this->assertDatabaseHas('articles', [
            'title'    => 'Sample Article',
            'source'   => 'Test Source',
            'url_hash' => hash('sha256', $data['url']),
        ]);
    }

    /** @test */
    public function it_updates_existing_article_if_url_matches()
    {
        $url  = 'https://example.com/article-1';
        $hash = hash('sha256', $url);

        Article::create(
            [
                'title'    => 'Old Title',
                'url'      => $url,
                'url_hash' => $hash,
                'source'   => 'Old Source',
            ]
        );

        $updatedData = [
            'title'        => 'Updated Title',
            'url'          => $url,
            'source'       => 'New Source',
            'published_at' => now()->toDateTimeString(),
        ];

        $this->articleService->storeMany([$updatedData], "Sample");

        $this->assertDatabaseHas('articles', [
            'url_hash' => $hash,
            'title'    => 'Updated Title',
            'source'   => 'New Source',
        ]);
    }

    /** @test */
    public function it_handles_store_many_and_skips_invalid()
    {
        $valid = [
            'title'        => 'Valid Article',
            'url'          => 'https://example.com/valid',
            'source'       => 'Source',
            'published_at' => now()->toDateTimeString(),
        ];

        $invalid = ['title' => 'Invalid without URL'];

        $this->articleService->storeMany([$valid, $invalid], "FakeCat");

        $this->assertDatabaseHas('articles', ['title' => 'Valid Article']);
        $this->assertDatabaseMissing('articles', ['title' => 'Invalid without URL']);
    }
}
