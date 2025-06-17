<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'primary_contact_name' => fake()->name(),
            'primary_contact_email' => fake()->unique()->safeEmail(),
            'primary_contact_phone' => fake()->phoneNumber(),
            'primary_contact_title' => fake()->jobTitle(),
            'preferred_comms_method' => fake()->randomElement(['email', 'phone', 'text']),
            'hubspot_company_record' => fake()->randomNumber(8),
            'signing_date' => fake()->dateTimeBetween('-2 years', 'now'),
            'status' => fake()->randomElement(['active', 'inactive', 'archived', 'on_hold']),
            'notes' => fake()->optional()->paragraph(),
        ];
    }

    /**
     * Indicate that the client is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the client is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}