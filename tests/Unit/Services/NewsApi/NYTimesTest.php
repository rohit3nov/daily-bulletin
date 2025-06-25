<?php

namespace Tests\Unit\Services;

use App\Services\NewsApi\NYTimes;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NYTimesTest extends TestCase
{
    /** @test */
    public function it_fetches_and_maps_articles_correctly()
    {
        // Configure NYTimes API
        Config::set('services.newsapi.sources.nytimes', [
            'url'          => 'https://api.nytimes.com',
            'endpoint'     => 'svc/topstories/v2/home.json',
            'search_key'   => 'q',
            'response_key' => 'results',
            'rate_limit'   => 10,
            'query_params' => [
                'api-key' => 'fake-key',
            ],
            'mapping'      => [
                'title'        => 'title',
                'description'  => 'abstract',
                'url'          => 'url',
                'url_to_image' => 'multimedia.0.url',
                'published_at' => 'published_date',
                'source'       => 'New York Times',
                'source_id'    => 'nytimes',
                'author'       => 'byline',
                'content'      => 'content',
            ]
        ]);

        Http::fake(
            [
                'https://api.nytimes.com/*' => Http::response(
                    [
                        'results' =>
                            [
                                [
                                    'title'          => 'New HIV prevention shot approved',
                                    'abstract'       => 'Yeztugo is a breakthrough...',
                                    'url'            => 'https://www.nytimes.com/2025/06/21/hiv-shot',
                                    'multimedia'     => [
                                        ['url' => 'https://nytimes.com/image1.jpg']
                                    ],
                                    'published_date' => now()->toIso8601String(),
                                    'byline'         => 'Health Reporter',
                                    'content'        => 'Sample NYT article content',
                                ]
                            ]
                    ],
                    200
                )
            ]
        );

        $service  = new NYTimes();
        $articles = $service->fetch('Health');

        $this->assertCount(1, $articles);
        $this->assertEquals('New HIV prevention shot approved', $articles[0]['title']);
        $this->assertEquals('New York Times', $articles[0]['source']);
        $this->assertEquals('https://www.nytimes.com/2025/06/21/hiv-shot', $articles[0]['url']);
        $this->assertEquals('Health Reporter', $articles[0]['author']);
    }
}
