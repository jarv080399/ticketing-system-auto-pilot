<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\KbArticle;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KbArticleVersion>
 */
class KbArticleVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'article_id' => KbArticle::inRandomOrder()->first()->id ?? KbArticle::factory()->create()->id,
            'version_number' => 1,
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(5, true),
            'changed_by' => User::inRandomOrder()->first()->id ?? User::factory()->create(['role' => 'agent'])->id,
            'change_summary' => fake()->sentence(),
        ];
    }
}
