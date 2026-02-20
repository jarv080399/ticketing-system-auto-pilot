<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\KbCategory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KbArticle>
 */
class KbArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(5, true),
            'excerpt' => fake()->paragraph(),
            'visibility' => fake()->randomElement(['public', 'public', 'public', 'internal']), // 75% public
            'status' => fake()->randomElement(['draft', 'published', 'published', 'published', 'archived']), // mostly published
            'author_id' => User::inRandomOrder()->first()->id ?? User::factory()->create(['role' => 'agent'])->id,
            'category_id' => KbCategory::inRandomOrder()->first()->id ?? KbCategory::factory()->create()->id,
            'view_count' => fake()->numberBetween(0, 1000),
            'helpful_yes' => fake()->numberBetween(0, 50),
            'helpful_no' => fake()->numberBetween(0, 10),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
