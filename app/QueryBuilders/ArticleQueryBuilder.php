<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ArticleQueryBuilder
{
    public function __construct(protected Builder $query) {}

    public function apply(Request $request): Builder
    {
        return $this
            ->filterByCategory($request)
            ->filterByKeyword($request)
            ->filterByDate($request)
            ->filterBySource($request)
            ->query;
    }

    protected function filterByCategory(Request $request): self
    {
        if ($request->filled('category')) {
            $this->query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        return $this;
    }

    protected function filterByKeyword(Request $request): self
    {
        if ($request->filled('q')) {
            $this->query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                    ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        return $this;
    }

    protected function filterByDate(Request $request): self
    {
        if ($request->filled('date')) {
            $this->query->whereDate('published_at', $request->date);
        }

        return $this;
    }

    protected function filterBySource(Request $request): self
    {
        if ($request->filled('source')) {
            $this->query->where('source', $request->source);
        }

        return $this;
    }
}
