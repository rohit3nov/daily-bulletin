<?php

namespace Tests\Unit\Services\NewsApi;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\NewsApi\NYTimes;

class NYTimesTest extends TestCase
{
    public function test_it_returns_multiple_articles_in_normalized_format()
    {
        Http::fake(
            [
                '*' => Http::response(
                    [
                        'results' =>
                            [
                                [
                                    'title'          => 'NYT Article One',
                                    'abstract'       => 'Abstract one',
                                    'url'            => 'https://nytimes.com/article-one',
                                    'multimedia'     => [
                                        ['url' => 'https://nytimes.com/img1.jpg']
                                    ],
                                    'published_date' => '2025-06-20T08:00:00Z',
                                    'section'        => 'Opinion',
                                    'byline'         => 'By Author One'
                                ],
                                [
                                    'title'          => 'NYT Article Two',
                                    'abstract'       => 'Abstract two',
                                    'url'            => 'https://nytimes.com/article-two',
                                    'multimedia'     => [
                                        ['url' => 'https://nytimes.com/img2.jpg']
                                    ],
                                    'published_date' => '2025-06-20T09:00:00Z',
                                    'section'        => 'Tech',
                                    'byline'         => 'By Author Two'
                                ]
                            ]
                    ]
                )
            ]
        );

        $articles = (new NYTimes())->fetch();

        $this->assertCount(2, $articles);
        $this->assertEquals('NYT Article One', $articles[0]['title']);
        $this->assertEquals('By Author Two', $articles[1]['author']);
    }
}
