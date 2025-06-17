<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Business;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'account_manager_id' => User::factory(),
            'business_id' => Business::factory(),
            'plan_id' => Plan::factory(),
            'monday_pulse_id' => fake()->optional()->randomNumber(8),
            'monday_board_id' => fake()->optional()->randomNumber(8),
            'portfolio_project_rag' => fake()->optional()->randomElement(['red', 'amber', 'green']),
            'portfolio_project_doc' => fake()->optional()->randomElements(['doc1', 'doc2', 'doc3'], fake()->numberBetween(0, 3)),
            'portfolio_project_scope' => fake()->optional()->paragraph(),
            'project_url' => fake()->url(),
            'current_services' => fake()->randomElements(['seo', 'ppc', 'social', 'content'], fake()->numberBetween(1, 4)),
            'completed_services' => fake()->randomElements(['design', 'development', 'setup'], fake()->numberBetween(0, 3)),
            'specialist_monday_id' => fake()->optional()->randomNumber(6),
            'content_writer_monday_id' => fake()->optional()->randomNumber(6),
            'developer_monday_id' => fake()->optional()->randomNumber(6),
            'copywriter_monday_id' => fake()->optional()->randomNumber(6),
            'designer_monday_id' => fake()->optional()->randomNumber(6),
            'google_drive_folder' => fake()->optional()->url(),
            'client_logo' => fake()->optional()->imageUrl(),
            'slack_channel' => fake()->optional()->word(),
            'bright_local_url' => fake()->optional()->url(),
            'google_sheet_id' => fake()->optional()->uuid(),
            'wp_umbrella_project_id' => fake()->optional()->uuid(),
            'project_start_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'my_maps_share_link' => fake()->optional()->url(),
            'status' => fake()->randomElement(['active', 'inactive', 'completed', 'on_hold']),
        ];
    }

    /**
     * Indicate that the project is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the project has a valid tracking domain.
     */
    public function withTrackingDomain(): static
    {
        return $this->state(fn (array $attributes) => [
            'project_url' => 'https://example-business.com',
        ]);
    }
}