<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\QueryBuilders\ArticleQueryBuilder;
use App\Http\Resources\ArticleResource;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with('category');

        $filteredQuery = (new ArticleQueryBuilder($query))->apply($request);

        return ArticleResource::collection($filteredQuery->latest()->paginate(10));
    }

    public function show(Article $article)
    {
        $article->load('category');

        return new ArticleResource($article);
    }
}
