<?php

namespace Database\Factories;

use App\Enums\OffPeriodeStatus;
use App\Enums\OffPeriodeType;
use App\Models\Company;
use App\Models\Employee;
use App\Models\OffPeriode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OffPeriode>
 */
class OffPeriodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = now()->addWeek()->startOfDay();

        return [
            'company_id' => Company::factory(),
            'employee_id' => Employee::factory(),
            'type' => fake()->randomElement(OffPeriodeType::cases()),
            'status' => OffPeriodeStatus::Pending,
            'starts_on' => $start,
            'ends_on' => $start->copy()->addDays(4),
            'reason' => fake()->optional()->sentence(),
            'internal_note' => null,
            'decided_by' => null,
            'decided_at' => null,
        ];
    }
}
