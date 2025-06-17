<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'client_id' => null, // Can be set explicitly or via withClient() state
            'phone' => fake()->optional()->phoneNumber(),
            'title' => fake()->optional()->jobTitle(),
            'is_active' => true,
            'monday_user_id' => fake()->optional()->randomNumber(6),
            'monday_photo_url' => fake()->optional()->imageUrl(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Associate the user with a client.
     */
    public function withClient($client = null): static
    {
        return $this->state(fn (array $attributes) => [
            'client_id' => $client instanceof \App\Models\Client ? $client->id : ($client ?: \App\Models\Client::factory()),
        ]);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'client_id' => null,
        ]);
    }
}
