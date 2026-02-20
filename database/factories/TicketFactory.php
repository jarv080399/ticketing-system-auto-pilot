<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement(['new', 'in_progress', 'waiting_on_customer', 'resolved', 'closed']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'category_id' => Category::factory(),
            'requester_id' => User::factory(),
            'agent_id' => null,
            'source' => $this->faker->randomElement(['web', 'email', 'slack', 'teams']),
            'tags' => $this->faker->words(3),
        ];
    }
}
