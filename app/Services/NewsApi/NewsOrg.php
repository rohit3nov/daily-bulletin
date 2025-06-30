<?php

namespace app\Services\NewsApi;

use App\Contracts\NewsApiInterface;
use App\Services\NewsApi\AbstractNewsApiService;


class NewsOrg extends AbstractNewsApiService implements NewsApiInterface
{
    public const API_NAME = 'NewsOrg';
}
