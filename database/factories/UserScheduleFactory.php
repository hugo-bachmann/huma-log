<?php

namespace Database\Factories;

use App\Enums\ScheduleStatus;
use App\Models\Company;
use App\Models\UserSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserSchedule>
 */
class UserScheduleFactory extends Factory
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
            'week_start_date' => now()->startOfWeek(),
            'status' => ScheduleStatus::Draft,
            'published_at' => null,
        ];
    }
}
