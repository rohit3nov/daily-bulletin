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
        $articles = ArticleQueryBuilder::apply($request, Article::with('category'))
            ->latest('published_at')
            ->paginate($request->input('per_page', 10));

        return ArticleResource::collection($articles);
    }

    public function show($id)
    {
        $article = Article::with('category')->findOrFail($id);

        return ArticleResource::collection($article);
    }
}
