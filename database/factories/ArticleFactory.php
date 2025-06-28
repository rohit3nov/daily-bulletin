<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'title'        => $this->faker->sentence,
            'description'  => $this->faker->paragraph,
            'url'          => $this->faker->url,
            'url_to_image' => $this->faker->imageUrl,
            'published_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'source'       => $this->faker->randomElement(['BBC', 'CNN', 'Fortune', 'NPR']),
            'source_id'    => $this->faker->uuid,
            'author'       => $this->faker->name,
            'content'      => $this->faker->paragraphs(2, true),
            'category_id'  => Category::factory(),
        ];
    }
}
