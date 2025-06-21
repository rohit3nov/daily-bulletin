<?php

use Illuminate\Support\Facades\Route;
use App\Services\NewsFetchers\NewsApiFetcher;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-newsapi', function (NewsApiFetcher $fetcher) {
    $fetcher->fetchAndStore();
    return 'Articles fetched and stored!';
});
