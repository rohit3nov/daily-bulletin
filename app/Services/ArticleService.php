<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use Carbon\Carbon;

class ArticleService
{
    public function storeMany(array $articles, string $categoryName): void
    {
        if (empty($articles)) {
            return;
        }

        $category = Category::firstOrCreate(['name' => $categoryName]);

        foreach ($articles as $article) {
            $this->createOrUpdate($article, $category->id);
        }
    }

    public function createOrUpdate(array $data, int $categoryId): void
    {
        if (empty($data['url'])) {
            return;
        }

        $urlHash = hash('sha256', $data['url']);

        Article::updateOrCreate(
            ['url_hash' => $urlHash],
            [
                'title'        => $data['title'] ?? null,
                'description'  => $data['description'] ?? null,
                'content'      => $data['content'] ?? null,
                'url'          => $data['url'],
                'url_hash'     => $urlHash,
                'url_to_image' => $data['url_to_image'] ?? null,
                'published_at' => isset($data['published_at']) ? Carbon::parse($data['published_at']) : now(),
                'source'       => $data['source'] ?? null,
                'source_id'    => $data['source_id'] ?? null,
                'author'       => $data['author'] ?? null,
                'category_id'  => $categoryId,
            ]
        );
    }
}
