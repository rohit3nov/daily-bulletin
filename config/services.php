<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'newsapi' => [
        'categories' => [
            'General',
            'Breaking News',
            'World News',
            'Politics',
            'Business',
            'Technology',
            'Science',
            'Health',
            'Sports',
            'Entertainment',
            'Lifestyle',
            'Education',
            'Environment',
            'Arts & Culture',
            'Travel',
            'Food & Dining',
            'Opinion',
            'Weather',
            'Crime',
            'Real Estate',
            'Markets',
        ],
        'sources'    => [
            'NewsOrg' => [
                'url'          => 'https://newsapi.org',
                'endpoint'     => '/v2/top-headlines',
                'search_key'   => 'q',
                'response_key' => 'articles',
                'rate_limit'   => 10,
                'query_params'  => [
                    'country'  => 'us',
                    'pageSize' => 20,
                    'apiKey'   => env('NEWSAPI_ORG_API_KEY'),
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
            ],
            'Guardian'    => [
                'url'          => 'https://content.guardianapis.com',
                'endpoint'     => 'search',
                'search_key'   => 'q',
                'response_key' => 'response.results',
                'rate_limit'   => 10,
                'query_params'  => [
                    'show-fields' => 'thumbnail,bodyText,byline',
                    'api-key'     => env('GUARDIAN_API_KEY'),
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
            ],
            'NYTimes'     => [
                'url'          => 'https://api.nytimes.com',
                'endpoint'     => 'svc/topstories/v2/home.json',
                'search_key'   => 'q',
                'response_key' => 'results',
                'rate_limit'   => 10,
                'query_params'  => [
                    'api-key' => env('NYTIMES_API_KEY'),
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
            ]
        ]

    ],

];
