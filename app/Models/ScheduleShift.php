<?php

namespace App\Models;

use Database\Factories\ScheduleShiftFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_schedule_id', 'employee_id', 'user_job_id', 'starts_at', 'ends_at'])]
class ScheduleShift extends Model
{
    /** @use HasFactory<ScheduleShiftFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    /**
     * Durée du créneau en heures décimales.
     */
    protected function durationHours(): Attribute
    {
        return Attribute::get(fn (): float => round($this->starts_at->diffInMinutes($this->ends_at) / 60, 2));
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(UserSchedule::class, 'user_schedule_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function userJob(): BelongsTo
    {
        return $this->belongsTo(UserJob::class);
    }
}
