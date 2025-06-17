<?php

namespace Database\Factories;

use App\Models\Update;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Update>
 */
class UpdateFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(2, true),
            'type' => fake()->randomElement(['progress', 'milestone', 'issue', 'general']),
            'is_published' => fake()->boolean(80),
            'published_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the update is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the update is a milestone.
     */
    public function milestone(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'milestone',
            'is_published' => true,
        ]);
    }
}