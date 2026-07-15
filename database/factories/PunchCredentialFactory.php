<?php

namespace Database\Factories;

use App\Models\PunchCredential;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PunchCredential>
 */
class PunchCredentialFactory extends Factory
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
            'type' => \App\Enums\CredentialType::Nfc,
            'identifier' => strtoupper(fake()->unique()->bothify('##:##:##:##')),
            'label' => fake()->randomElement(['Badge bleu', 'Carte NFC', null]),
            'is_active' => true,
            'last_used_at' => null,
        ];
    }
}
