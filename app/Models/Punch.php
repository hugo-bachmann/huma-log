<?php

namespace App\Models;

use App\Enums\PunchDirection;
use App\Enums\PunchMethod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Append-only clock event. Never updated: a correction is a new Punch.
 * Each row chains to the previous one (per company) via previous_hash/hash,
 * so any tampering breaks the chain and is detectable.
 */
#[Fillable(['company_id', 'employee_id', 'punch_device_id', 'punch_credential_id', 'direction', 'method', 'punched_at', 'latitude', 'longitude', 'is_suspect', 'note', 'created_by'])]
class Punch extends Model
{
    /** @use HasFactory<\Database\Factories\PunchFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_suspect' => false,
    ];

    /**
     * Minutes of allowed drift between the declared punch time and server
     * receipt before a (non-manual) punch is flagged suspect.
     * ponytail: fixed threshold; make it a Company setting if clients need it tuned.
     */
    public const SUSPECT_DRIFT_MINUTES = 5;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'direction' => PunchDirection::class,
            'method' => PunchMethod::class,
            'punched_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_suspect' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Punch $punch): void {
            $previous = static::query()
                ->where('company_id', $punch->company_id)
                ->orderByDesc('id')
                ->first();

            $punch->previous_hash = $previous?->hash;
            $punch->hash = $punch->computeHash();

            // Device punches should be near-real-time; large drift = suspect.
            // Manual entries are expected to be backdated, so skip the check.
            if (! $punch->is_suspect && $punch->method !== PunchMethod::Manual) {
                $punch->is_suspect = $punch->punched_at->diffInMinutes(now()) > self::SUSPECT_DRIFT_MINUTES;
            }
        });
    }

    /**
     * Recompute the tamper-evidence hash from this row's own fields.
     * Comparing it against the stored `hash` verifies the row is untouched.
     */
    public function computeHash(): string
    {
        return hash('sha256', implode('|', [
            $this->previous_hash,
            $this->employee_id,
            $this->direction->value,
            $this->punched_at->toIso8601String(),
            $this->method->value,
        ]));
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(PunchDevice::class, 'punch_device_id');
    }

    public function credential(): BelongsTo
    {
        return $this->belongsTo(PunchCredential::class, 'punch_credential_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
