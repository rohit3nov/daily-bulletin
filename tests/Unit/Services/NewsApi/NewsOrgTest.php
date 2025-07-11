<?php

namespace Tests\Unit\Services;

use App\Services\NewsApi\NewsOrg;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;


class NewsOrgTest extends TestCase
{
    /** @test */
    public function it_fetches_and_maps_articles_correctly()
    {
        // Mock config for NewsOrg
        Config::set('services.newsapi.sources.newsorg', [
            'url'          => 'https://newsapi.org',
            'endpoint'     => '/v2/top-headlines',
            'search_key'   => 'q',
            'response_key' => 'articles',
            'rate_limit'   => 10,
            'query_params' => [
                'country'  => 'us',
                'pageSize' => 20,
                'apiKey'   => 'fake-key',
            ],
            'mapping'      => [
                'title'        => 'title',
                'description'  => 'description',
                'url'          => 'url',
                'url_to_image' => 'urlToImage',
                'published_at' => 'publishedAt',
                'source'       => 'source.name',
                'source_id'    => 'source.id',
                'author'       => 'author',
                'content'      => 'content',
            ]
        ]);

        // Mock the API response
        Http::fake(
            [
                'https://newsapi.org/*' => Http::response(
                    [
                        'status'       => 'ok',
                        'totalResults' => 1,
                        'articles'     => [
                            [
                                'source'      => ['id' => null, 'name' => 'BBC News'],
                                'author'      => null,
                                'title'       => 'Minnesota shootings suspect was a prepper',
                                'description' => 'After shooting two local politicians...',
                                'url'         => 'https://www.bbc.com/news/articles/c89ev9j955ko',
                                'urlToImage'  => 'https://ichef.bbci.co.uk/news/1024/branded_news/sample1.jpg',
                                'publishedAt' => now()->toIso8601String(),
                                'content'     => 'Sample content 1'
                            ]
                        ]
                    ],
                    200
                )
            ]
        );

        $service  = new NewsOrg();
        $articles = $service->fetch('Politics');

        $this->assertCount(1, $articles);
        $this->assertEquals('Minnesota shootings suspect was a prepper', $articles[0]['title']);
        $this->assertEquals('BBC News', $articles[0]['source']);
        $this->assertEquals('https://www.bbc.com/news/articles/c89ev9j955ko', $articles[0]['url']);
    }

    public function test_it_handles_server_error_gracefully()
    {
        Http::fake(
            [
                '*' => Http::response([], 500),
            ]
        );

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Failed to fetch from NewsOrg');
            });

        $service = new NewsOrg();
        $result  = $service->fetch('business');

        $this->assertEquals([], $result);
    }

    public function test_it_handles_timeout_gracefully()
    {
        Http::fake(
            [
                '*' => fn() => throw new \Exception('cURL timeout'),
            ]
        );

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Failed to fetch from NewsOrg');
            });

        $service = new NewsOrg();
        $result  = $service->fetch('technology');

        $this->assertEquals([], $result);
    }

    public function test_it_handles_malformed_json_response()
    {
        Http::fake(
            [
                '*' => Http::response('not json', 200),
            ]
        );

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Failed to fetch from NewsOrg');
            });

        $service = new NewsOrg();
        $result  = $service->fetch('science');

        $this->assertEquals([], $result);
    }
}
