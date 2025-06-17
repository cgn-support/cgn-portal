<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $month = fake()->numberBetween(1, 12);
        $monthName = date('F', mktime(0, 0, 0, $month, 1));
        $year = fake()->dateTimeBetween('-1 year', 'now')->format('Y');
        
        return [
            'project_id' => Project::factory(),
            'account_manager_id' => User::factory(),
            'title' => "Marketing Report For {$monthName} {$year}",
            'report_date' => fake()->dateTimeBetween('-3 months', 'now'),
            'report_month' => $month,
            'content' => fake()->optional()->paragraph(),
            'metrics_data' => null,
            'file_path' => fake()->optional()->filePath(),
            'status' => fake()->randomElement(['draft', 'sent', 'reviewed']),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the report is sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
        ]);
    }

    /**
     * Indicate that the report is reviewed.
     */
    public function reviewed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'reviewed',
        ]);
    }
}