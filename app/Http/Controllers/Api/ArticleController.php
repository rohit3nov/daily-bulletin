<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\QueryBuilders\ArticleQueryBuilder;
use App\Http\Resources\ArticleResource;
use Illuminate\Http\Request;

/**
 * @group Articles
 * View and search articles.
 */
class ArticleController extends Controller
{
    /**
     * Get a paginated list of articles with filters.
     *
     * @queryParam keyword string Search term (optional).
     * @queryParam category string Category name (optional).
     * @queryParam source string Source name (optional).
     * @queryParam date date Format: YYYY-MM-DD (optional).
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "NASA Psyche returns to full thrust",
     *       "source": "The Guardian"
     *     }
     *   ],
     *   "links": {...},
     *   "meta": {...}
     * }
     */
    public function index(Request $request)
    {
        $query = Article::with('category');

        $filteredQuery = (new ArticleQueryBuilder($query))->apply($request);

        return ArticleResource::collection($filteredQuery->latest()->paginate(10));
    }

    /**
     * Get article details by ID.
     *
     * @urlParam id int required The ID of the article.
     *
     * @response 200 {
     *   "id": 1,
     *   "title": "NASA Psyche returns to full thrust",
     *   "source": "The Guardian"
     * }
     */
    public function show(Article $article)
    {
        $article->load('category');

        return new ArticleResource($article);
    }
}
