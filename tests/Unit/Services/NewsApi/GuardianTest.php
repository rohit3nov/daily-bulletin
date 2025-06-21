<?php


namespace Tests\Unit\Services\NewsApi;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\NewsApi\Guardian;

class GuardianTest extends TestCase
{
    public function test_it_returns_multiple_articles_in_normalized_format()
    {
        Http::fake(
            [
                '*' => Http::response(
                    [
                        'response' =>
                            [
                                'results' =>
                                    [
                                        [
                                            'webTitle'           => 'Article One',
                                            'webUrl'             => 'https://example.com/article-one',
                                            'webPublicationDate' => '2025-06-20T10:00:00Z',
                                            'fields'             => [
                                                'thumbnail' => 'https://example.com/image1.jpg',
                                                'bodyText'  => 'Content one'
                                            ],
                                            'sectionName'        => 'World',
                                            'pillarName'         => 'News',
                                            'tags'               => [['webTitle' => 'Author One']],
                                        ],
                                        [
                                            'webTitle'           => 'Article Two',
                                            'webUrl'             => 'https://example.com/article-two',
                                            'webPublicationDate' => '2025-06-20T11:00:00Z',
                                            'fields'             => [
                                                'thumbnail' => 'https://example.com/image2.jpg',
                                                'bodyText'  => 'Content two'
                                            ],
                                            'sectionName'        => 'Tech',
                                            'pillarName'         => 'Science',
                                            'tags'               => [['webTitle' => 'Author Two']],
                                        ],
                                    ]
                            ]
                    ]
                )
            ]
        );

        $articles = (new Guardian())->fetch();

        $this->assertCount(2, $articles);
        $this->assertEquals('Article One', $articles[0]['title']);
        $this->assertEquals('Article Two', $articles[1]['title']);
    }
}
