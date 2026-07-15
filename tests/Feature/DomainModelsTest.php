<?php

use App\Enums\CredentialType;
use App\Enums\PunchDirection;
use App\Enums\PunchMethod;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Punch;
use App\Models\PunchCredential;
use App\Models\PunchDevice;
use Illuminate\Database\QueryException;

test('company has employees, devices and punches', function () {
    $company = Company::factory()
        ->has(Employee::factory()->count(2))
        ->has(PunchDevice::factory()->count(1), 'punchDevices')
        ->create();

    expect($company->employees)->toHaveCount(2)
        ->and($company->punchDevices)->toHaveCount(1);
});

test('employee owns credentials and punches', function () {
    $employee = Employee::factory()
        ->has(PunchCredential::factory()->count(2), 'credentials')
        ->create();
    Punch::factory()->for($employee)->create();

    expect($employee->credentials)->toHaveCount(2)
        ->and($employee->punches)->toHaveCount(1)
        ->and($employee->company->is($employee->credentials->first()->employee->company))->toBeTrue();
});

test('a credential identifier is unique per type', function () {
    $employee = Employee::factory()->create();
    PunchCredential::factory()->for($employee)->create(['type' => CredentialType::Nfc, 'identifier' => 'AA:BB']);

    PunchCredential::factory()->for($employee)->create(['type' => CredentialType::Nfc, 'identifier' => 'AA:BB']);
})->throws(QueryException::class);

test('punches form a tamper-evident hash chain per company', function () {
    $employee = Employee::factory()->create();

    $first = Punch::factory()->for($employee)->create();
    $second = Punch::factory()->for($employee)->create(['direction' => PunchDirection::Out]);

    expect($first->previous_hash)->toBeNull()
        ->and($second->previous_hash)->toBe($first->hash)
        ->and($second->hash)->toBe($second->computeHash());
});

test('a punch drifting far from server time is flagged suspect', function () {
    $employee = Employee::factory()->create();

    $onTime = Punch::factory()->for($employee)->create(['punched_at' => now()]);
    $drifted = Punch::factory()->for($employee)->create(['punched_at' => now()->subHour()]);
    $manual = Punch::factory()->for($employee)->create([
        'punched_at' => now()->subDay(),
        'method' => PunchMethod::Manual,
    ]);

    expect($onTime->is_suspect)->toBeFalse()
        ->and($drifted->is_suspect)->toBeTrue()
        ->and($manual->is_suspect)->toBeFalse();
});
