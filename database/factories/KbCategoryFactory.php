<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KbCategory>
 */
class KbCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'icon' => fake()->randomElement(['💻', '🖨️', '🔑', '🌐', '📱', '🛡️']),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
