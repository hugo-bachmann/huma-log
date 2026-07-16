<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
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
            'company_name' => fake()->company(),
            'size' => fake()->randomElement(['1-10', '11-30', '31-50', '50+']),
            'source' => fake()->randomElement(['Landing /demo', 'Landing /pricing', 'Google Ads', 'LinkedIn', 'Bouche-à-oreille', 'Newsletter', 'Référencement']),
            'message' => fake()->optional()->sentence(),
            'status' => LeadStatus::New,
        ];
    }
}
