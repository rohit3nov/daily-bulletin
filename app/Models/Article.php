<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'url',
        'url_to_image',
        'published_at',
        'source',
        'source_id',
        'author',
        'category_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            $article->url_hash = hash('sha256', $article->url);
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
