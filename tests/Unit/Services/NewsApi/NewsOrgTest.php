<?php

namespace tests\Unit\Services\NewsApi;

use App\Services\NewsApi\NewsOrg;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;


class NewsOrgTest extends TestCase
{
    public function test_it_fetches_articles_from_newsorgapi_and_parses_them_correctly()
    {
        Http::fake(
            [
                'newsapi.org/*' => Http::response(
                    [
                        'status'       => 'ok',
                        'totalResults' => 2,
                        'articles'     => [
                            [
                                'source'      => [
                                    'id'   => 'cnn',
                                    'name' => 'CNN'
                                ],
                                'author'      => 'John Doe',
                                'title'       => 'Test Title 1',
                                'description' => 'Test description 1',
                                'url'         => 'https://example.com/article1',
                                'urlToImage'  => 'https://example.com/image1.jpg',
                                'publishedAt' => '2025-06-20T10:00:00Z',
                                'content'     => 'Content 1',
                            ],
                            [
                                'source'      => [
                                    'id'   => 'bbc-news',
                                    'name' => 'BBC News'
                                ],
                                'author'      => 'Jane Smith',
                                'title'       => 'Test Title 2',
                                'description' => 'Test description 2',
                                'url'         => 'https://example.com/article2',
                                'urlToImage'  => 'https://example.com/image2.jpg',
                                'publishedAt' => '2025-06-20T11:00:00Z',
                                'content'     => 'Content 2',
                            ]
                        ],
                    ],
                    200
                )
            ]
        );

        $fetcher  = new NewsOrg();
        $articles = $fetcher->fetch();

        $this->assertCount(2, $articles);

        $this->assertEquals('Test Title 1', $articles[0]['title']);
        $this->assertEquals('https://example.com/article1', $articles[0]['url']);
        $this->assertEquals('cnn', $articles[0]['source_id']);
        $this->assertEquals('CNN', $articles[0]['source']);

        $this->assertEquals('Test Title 2', $articles[1]['title']);
        $this->assertEquals('https://example.com/article2', $articles[1]['url']);
        $this->assertEquals('bbc-news', $articles[1]['source_id']);
        $this->assertEquals('BBC News', $articles[1]['source']);
    }
}
