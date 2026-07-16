<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\ScheduleShift;
use App\Models\UserSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ScheduleShift>
 */
class ScheduleShiftFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = now()->startOfWeek()->setTime(8, 0);

        return [
            'user_schedule_id' => UserSchedule::factory(),
            'employee_id' => Employee::factory(),
            'user_job_id' => null,
            'starts_at' => $start,
            'ends_at' => $start->copy()->addHours(8),
        ];
    }
}
