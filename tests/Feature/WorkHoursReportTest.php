<?php

use App\Enums\PunchDirection;
use App\Enums\PunchMethod;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Punch;
use App\Services\WorkHoursReport;

function punch(Employee $employee, PunchDirection $direction, string $at): void
{
    Punch::factory()->for($employee)->create([
        'direction' => $direction,
        'method' => PunchMethod::Manual,
        'punched_at' => $at,
    ]);
}

test('it pairs in and out punches into worked minutes', function () {
    $company = Company::factory()->create();
    $employee = Employee::factory()->for($company)->create();

    punch($employee, PunchDirection::In, '2026-05-04 08:00');
    punch($employee, PunchDirection::Out, '2026-05-04 12:00');
    punch($employee, PunchDirection::In, '2026-05-04 13:00');
    punch($employee, PunchDirection::Out, '2026-05-04 17:30');

    $report = new WorkHoursReport($company, now()->parse('2026-05-01'), now()->parse('2026-05-31')->endOfDay());

    // 4h + 4h30 = 8h30 = 510 min
    expect($report->totalMinutes())->toBe(510);
});

test('a dangling in without an out is ignored', function () {
    $company = Company::factory()->create();
    $employee = Employee::factory()->for($company)->create();

    punch($employee, PunchDirection::In, '2026-05-04 08:00');
    punch($employee, PunchDirection::Out, '2026-05-04 12:00');
    punch($employee, PunchDirection::In, '2026-05-04 14:00'); // never closed

    $report = new WorkHoursReport($company, now()->parse('2026-05-01'), now()->parse('2026-05-31')->endOfDay());

    expect($report->totalMinutes())->toBe(240); // only the 08:00→12:00 pair
});

test('punches outside the period are excluded', function () {
    $company = Company::factory()->create();
    $employee = Employee::factory()->for($company)->create();

    punch($employee, PunchDirection::In, '2026-04-30 08:00');
    punch($employee, PunchDirection::Out, '2026-04-30 12:00');
    punch($employee, PunchDirection::In, '2026-05-04 08:00');
    punch($employee, PunchDirection::Out, '2026-05-04 12:00');

    $report = new WorkHoursReport($company, now()->parse('2026-05-01'), now()->parse('2026-05-31')->endOfDay());

    expect($report->totalMinutes())->toBe(240); // only the May pair
});

test('per employee rows only include those with worked time', function () {
    $company = Company::factory()->create();
    $worked = Employee::factory()->for($company)->create();
    Employee::factory()->for($company)->create(); // no punches

    punch($worked, PunchDirection::In, '2026-05-04 08:00');
    punch($worked, PunchDirection::Out, '2026-05-04 12:00');

    $report = new WorkHoursReport($company, now()->parse('2026-05-01'), now()->parse('2026-05-31')->endOfDay());

    expect($report->perEmployee())->toHaveCount(1)
        ->and($report->perEmployee()->first()['employee']->id)->toBe($worked->id);
});

test('it formats minutes as hours and minutes', function () {
    expect(WorkHoursReport::formatMinutes(510))->toBe('8:30')
        ->and(WorkHoursReport::formatMinutes(148 * 60 + 5))->toBe('148:05')
        ->and(WorkHoursReport::formatMinutes(0))->toBe('0:00');
});
