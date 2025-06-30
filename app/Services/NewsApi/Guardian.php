<?php

namespace app\Services\NewsApi;

use App\Contracts\NewsApiInterface;
use App\Services\NewsApi\AbstractNewsApiService;

class Guardian extends AbstractNewsApiService implements NewsApiInterface
{
    public const API_NAME = 'Guardian';
}
