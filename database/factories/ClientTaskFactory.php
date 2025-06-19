<?php

namespace Database\Factories;

use App\Models\ClientTask;
use App\Models\PresetTask;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientTaskFactory extends Factory
{
    protected $model = ClientTask::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'preset_task_id' => PresetTask::factory(),
            'assigned_by' => User::factory(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'link' => $this->faker->optional(0.3)->url(), // 30% chance of having a link
            'is_completed' => false,
            'completed_at' => null,
            'due_date' => $this->faker->optional(0.7)->dateTimeBetween('now', '+30 days'), // 70% chance of having due date
            'completion_notes' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
            'completed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'completion_notes' => $this->faker->optional(0.6)->sentence(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => false,
            'completed_at' => null,
            'completion_notes' => null,
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => false,
            'completed_at' => null,
            'due_date' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    public function withLink(string $link = null): static
    {
        return $this->state(fn (array $attributes) => [
            'link' => $link ?? $this->faker->url(),
        ]);
    }

    public function withDueDate(\DateTimeInterface $dueDate): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => $dueDate,
        ]);
    }
}