<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'status' => fake()->randomElement(['new', 'valid', 'invalid', 'closed']),
            'value' => fake()->optional()->randomFloat(2, 100, 10000),
            'is_valid' => fake()->boolean(60),
            'notes' => fake()->optional()->paragraph(),
            'submitted_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'ip_address' => fake()->optional()->ipv4(),
            'referrer_name' => fake()->optional()->domainName(),
            'utm_source' => fake()->optional()->word(),
            'utm_medium' => fake()->optional()->word(),
            'utm_campaign' => fake()->optional()->word(),
            'payload' => [
                'form_data' => [
                    'name' => fake()->name(),
                    'email' => fake()->safeEmail(),
                    'message' => fake()->paragraph(),
                ],
                'tracking_data' => [
                    'session_id' => fake()->uuid(),
                    'utm_source' => fake()->optional()->word(),
                    'utm_medium' => fake()->optional()->word(),
                    'utm_campaign' => fake()->optional()->word(),
                ],
            ],
        ];
    }

    /**
     * Indicate that the lead is new.
     */
    public function newLead(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'new',
            'is_valid' => false,
        ]);
    }

    /**
     * Indicate that the lead is valid.
     */
    public function valid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'valid',
            'is_valid' => true,
        ]);
    }

    /**
     * Indicate that the lead is closed with value.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
            'is_valid' => true,
            'value' => fake()->randomFloat(2, 500, 10000),
        ]);
    }

    /**
     * Indicate that the lead has high value.
     */
    public function highValue(): static
    {
        return $this->state(fn (array $attributes) => [
            'value' => fake()->randomFloat(2, 5000, 25000),
        ]);
    }
}