<?php

namespace App\Models;

use App\Enums\OffPeriodeStatus;
use App\Enums\OffPeriodeType;
use Database\Factories\OffPeriodeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['company_id', 'employee_id', 'type', 'status', 'starts_on', 'ends_on', 'reason', 'internal_note', 'decided_by', 'decided_at'])]
class OffPeriode extends Model
{
    /** @use HasFactory<OffPeriodeFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => OffPeriodeType::class,
            'status' => OffPeriodeStatus::class,
            'starts_on' => 'date',
            'ends_on' => 'date',
            'decided_at' => 'datetime',
        ];
    }

    /**
     * Nombre de jours ouvrés (lundi–vendredi) couverts par l'absence, bornes incluses.
     */
    protected function workingDays(): Attribute
    {
        return Attribute::get(function (): int {
            $days = 0;

            foreach ($this->starts_on->toPeriod($this->ends_on) as $date) {
                if (! $date->isWeekend()) {
                    $days++;
                }
            }

            return $days;
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
