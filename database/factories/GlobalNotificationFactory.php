<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GlobalNotification>
 */
class GlobalNotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'content' => fake()->paragraph(2),
            'type' => fake()->randomElement(['announcement', 'feature', 'blog', 'podcast', 'video', 'general']),
            'icon' => null,
            'link' => fake()->optional(0.3)->url(),
            'is_active' => true,
            'published_at' => null,
            'expires_at' => null,
        ];
    }

    /**
     * Indicate that the notification is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the notification is scheduled for future publication.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => fake()->dateTimeBetween('+1 day', '+1 week'),
        ]);
    }

    /**
     * Indicate that the notification is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => fake()->dateTimeBetween('-2 weeks', '-1 week'),
            'expires_at' => fake()->dateTimeBetween('-1 week', '-1 day'),
        ]);
    }

    /**
     * Indicate that the notification is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }

    /**
     * Create notification of specific type.
     */
    public function announcement(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'announcement',
            'title' => 'Important Company Announcement',
            'content' => 'We have an important update to share with all our clients.',
        ]);
    }

    public function feature(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'feature',
            'title' => 'New Feature Release',
            'content' => 'Check out our latest feature that will improve your experience.',
        ]);
    }

    public function blog(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'blog',
            'title' => 'New Blog Post Published',
            'content' => 'Read our latest insights on industry trends and best practices.',
            'link' => 'https://example.com/blog/latest-post',
        ]);
    }

    public function withCustomIcon(): static
    {
        return $this->state(fn (array $attributes) => [
            'icon' => 'heroicon-o-star',
        ]);
    }
}