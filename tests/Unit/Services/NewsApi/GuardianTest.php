<?php

namespace Tests\Unit\Services;

use App\Services\NewsApi\Guardian;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;


class GuardianTest extends TestCase
{
    /** @test */
    public function it_fetches_and_maps_articles_correctly()
    {
        // Configure Guardian API
        Config::set('services.newsapi.sources.guardian', [
            'url'          => 'https://content.guardianapis.com',
            'endpoint'     => 'search',
            'search_key'   => 'q',
            'response_key' => 'response.results',
            'rate_limit'   => 10,
            'query_params' => [
                'show-fields' => 'thumbnail,bodyText,byline',
                'api-key'     => 'fake-key',
            ],
            'mapping'      => [
                'title'        => 'webTitle',
                'description'  => 'description',
                'url'          => 'webUrl',
                'url_to_image' => 'fields.thumbnail',
                'published_at' => 'webPublicationDate',
                'source'       => 'The Guardian',
                'source_id'    => 'guardian',
                'author'       => 'fields.byline',
                'content'      => 'fields.bodyText',
            ]
        ]);

        // Fake HTTP response
        Http::fake(
            [
                'https://content.guardianapis.com/*' => Http::response(
                    [
                        'response' => [
                            'results' => [
                                [
                                    'webTitle'           => 'NASA Psyche returns to full thrust',
                                    'description'        => 'NASA update...',
                                    'webUrl'             => 'https://guardian.com/space/psyche-returns',
                                    'fields'             => [
                                        'thumbnail' => 'https://guardian.com/images/sample3.jpg',
                                        'byline'    => 'Staff Writer',
                                        'bodyText'  => 'Sample content 3'
                                    ],
                                    'webPublicationDate' => now()->toIso8601String(),
                                ]
                            ]
                        ]
                    ],
                    200
                )
            ]
        );

        $service  = new Guardian();
        $articles = $service->fetch('Science');

        $this->assertCount(1, $articles);
        $this->assertEquals('NASA Psyche returns to full thrust', $articles[0]['title']);
        $this->assertEquals('The Guardian', $articles[0]['source']);
        $this->assertEquals('https://guardian.com/space/psyche-returns', $articles[0]['url']);
        $this->assertEquals('Staff Writer', $articles[0]['author']);
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
            ->withArgs(fn($msg) => str_contains($msg, 'Failed to fetch from Guardian'));

        $service = new Guardian();
        $result  = $service->fetch('World News');

        $this->assertEquals([], $result);
    }

    public function test_it_handles_timeout_gracefully()
    {
        Http::fake(
            [
                '*' => fn() => throw new \Exception('Request timed out'),
            ]
        );

        Log::shouldReceive('error')
            ->once()
            ->withArgs(fn($msg) => str_contains($msg, 'Failed to fetch from Guardian'));

        $service = new Guardian();
        $result  = $service->fetch('Technology');

        $this->assertEquals([], $result);
    }

    public function test_it_handles_malformed_json_response()
    {
        Http::fake(
            [
                '*' => Http::response('not-json', 200),
            ]
        );

        Log::shouldReceive('error')
            ->once()
            ->withArgs(fn($msg) => str_contains($msg, 'Failed to fetch from Guardian'));

        $service = new Guardian();
        $result  = $service->fetch('Education');

        $this->assertEquals([], $result);
    }
}
