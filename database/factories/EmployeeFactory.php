<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
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
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'job_title' => fake()->randomElement(['Serveur', 'Cuisinier', 'Plongeur', 'Réceptionniste']),
            'is_active' => true,
            'hired_at' => fake()->dateTimeBetween('-2 years')->format('Y-m-d'),
        ];
    }
}
