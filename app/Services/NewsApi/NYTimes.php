<?php

namespace app\Services\NewsApi;

use App\Contracts\NewsApiInterface;

class NYTimes extends AbstractNewsApiService implements NewsApiInterface
{
    public const API_NAME = 'NYTimes';

}
