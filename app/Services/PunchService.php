<?php

namespace App\Services;

use App\Enums\CredentialType;
use App\Enums\PunchDirection;
use App\Enums\PunchMethod;
use App\Exceptions\UnknownCredentialException;
use App\Models\Punch;
use App\Models\PunchCredential;
use App\Models\PunchDevice;

class PunchService
{
    /**
     * Resolve a scanned credential and record a clock event on the given device.
     *
     * Anti-fraud: the credential must be active AND belong to an employee of the
     * device's own company — a badge from another tenant is rejected.
     *
     * @throws UnknownCredentialException when no matching active credential exists
     */
    public function record(PunchDevice $device, CredentialType $type, string $identifier): Punch
    {
        $credential = PunchCredential::query()
            ->where('type', $type)
            ->where('identifier', $identifier)
            ->where('is_active', true)
            ->whereHas('employee', fn ($query) => $query->where('company_id', $device->company_id))
            ->with('employee')
            ->first();

        if (! $credential) {
            throw new UnknownCredentialException;
        }

        $employee = $credential->employee;

        // Auto-toggle: the direction is the opposite of the employee's last punch.
        // ponytail: pairs against the last punch ever — a forgotten checkout makes the
        // next day's first punch an "out". Add a daily reset / anomaly review when needed.
        $lastDirection = $employee->punches()
            ->orderByDesc('punched_at')
            ->orderByDesc('id')
            ->value('direction');

        $direction = $lastDirection === PunchDirection::In
            ? PunchDirection::Out
            : PunchDirection::In;

        $punch = Punch::create([
            'company_id' => $device->company_id,
            'employee_id' => $employee->id,
            'punch_device_id' => $device->id,
            'punch_credential_id' => $credential->id,
            'direction' => $direction,
            'method' => PunchMethod::from($type->value),
            'punched_at' => now(),
        ]);

        $credential->update(['last_used_at' => now()]);

        return $punch->setRelation('employee', $employee);
    }
}
