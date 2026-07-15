<?php

namespace Database\Factories;

use App\Models\PunchDevice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PunchDevice>
 */
class PunchDeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'name' => 'Borne '.fake()->word(),
            'location' => fake()->randomElement(['Entrée', 'Cuisine', 'Bar', 'Réception']),
            'token' => \Illuminate\Support\Str::random(40),
            'is_active' => true,
            'last_seen_at' => null,
        ];
    }
}
