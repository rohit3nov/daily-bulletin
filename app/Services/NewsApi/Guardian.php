<?php

namespace app\Services\NewsApi;

use App\Contracts\NewsApiInterface;

class Guardian extends AbstractNewsApiService implements NewsApiInterface
{
    public const API_NAME = 'Guardian';

}
