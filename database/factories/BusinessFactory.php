<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Business>
 */
class BusinessFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'name' => fake()->company(),
            'address_line1' => fake()->optional()->streetAddress(),
            'address_line2' => fake()->optional()->secondaryAddress(),
            'city' => fake()->optional()->city(),
            'state' => fake()->optional()->stateAbbr(),
            'zip_code' => fake()->optional()->postcode(),
            'country' => fake()->optional()->country(),
            'phone_number' => fake()->optional()->phoneNumber(),
            'website_url' => fake()->optional()->url(),
            'google_maps_url' => fake()->optional()->url(),
            'gbp_listing_id' => fake()->optional()->uuid(),
            'industry' => fake()->optional()->word(),
            'slack_channel_id' => fake()->optional()->word(),
            'status' => fake()->randomElement(['active', 'inactive', 'pending']),
            'notes' => fake()->optional()->paragraph(),
        ];
    }
}