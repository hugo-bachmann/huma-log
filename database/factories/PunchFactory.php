<?php

namespace Database\Factories;

use App\Models\Punch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Punch>
 */
class PunchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => \App\Models\Employee::factory(),
            'company_id' => fn (array $attributes) => \App\Models\Employee::find($attributes['employee_id'])->company_id,
            'punch_device_id' => null,
            'punch_credential_id' => null,
            'direction' => \App\Enums\PunchDirection::In,
            'method' => \App\Enums\PunchMethod::Nfc,
            'punched_at' => now(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'note' => null,
            'created_by' => null,
        ];
    }
}
