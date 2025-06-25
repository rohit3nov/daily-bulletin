<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ArticleQueryBuilder
{
    public static function apply(Request $request, Builder $query): Builder
    {
        if ($keyword = $request->input('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%")
                    ->orWhere('content', 'like', "%{$keyword}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->whereHas('category', fn($q) => $q->where('name', $category));
        }

        if ($source = $request->input('source')) {
            $query->where('source', $source);
        }

        if ($date = $request->input('date')) {
            $query->whereDate('published_at', $date);
        }

        return $query;
    }
}
