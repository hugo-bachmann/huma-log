<?php

namespace App\Models;

use App\Enums\ScheduleStatus;
use Database\Factories\UserScheduleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['company_id', 'week_start_date', 'status', 'published_at'])]
class UserSchedule extends Model
{
    /** @use HasFactory<UserScheduleFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'week_start_date' => 'date',
            'status' => ScheduleStatus::class,
            'published_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(ScheduleShift::class);
    }
}
