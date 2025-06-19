<?php

namespace Database\Factories;

use App\Models\PresetTask;
use Illuminate\Database\Eloquent\Factories\Factory;

class PresetTaskFactory extends Factory
{
    protected $model = PresetTask::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'sort_order' => $this->faker->numberBetween(1, 100),
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withSortOrder(int $sortOrder): static
    {
        return $this->state(fn (array $attributes) => [
            'sort_order' => $sortOrder,
        ]);
    }
}