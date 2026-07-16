<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\UserJob;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserJob>
 */
class UserJobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->randomElement(['Boulanger', 'Pâtissier', 'Caissier', 'Cuisine', 'Livraison', 'Serveur']),
            'color' => fake()->hexColor(),
            'description' => fake()->optional()->sentence(),
            'hourly_rate' => fake()->optional()->randomFloat(2, 11.88, 25),
        ];
    }
}
