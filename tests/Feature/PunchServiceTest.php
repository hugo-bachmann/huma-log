<?php

use App\Enums\CredentialType;
use App\Enums\PunchDirection;
use App\Enums\PunchMethod;
use App\Exceptions\UnknownCredentialException;
use App\Models\Company;
use App\Models\Employee;
use App\Models\PunchCredential;
use App\Models\PunchDevice;
use App\Services\PunchService;

beforeEach(function () {
    $this->service = new PunchService;
    $this->company = Company::factory()->create();
    $this->device = PunchDevice::factory()->for($this->company)->create();
    $this->employee = Employee::factory()->for($this->company)->create();
    $this->credential = PunchCredential::factory()->for($this->employee)->create([
        'type' => CredentialType::Nfc,
        'identifier' => 'BADGE-1',
    ]);
});

test('a valid scan records an in punch and stamps the credential', function () {
    $punch = $this->service->record($this->device, CredentialType::Nfc, 'BADGE-1');

    expect($punch->direction)->toBe(PunchDirection::In)
        ->and($punch->method)->toBe(PunchMethod::Nfc)
        ->and($punch->employee_id)->toBe($this->employee->id)
        ->and($punch->punch_device_id)->toBe($this->device->id)
        ->and($punch->company_id)->toBe($this->company->id)
        ->and($this->credential->fresh()->last_used_at)->not->toBeNull();
});

test('the direction auto-toggles on the next scan', function () {
    $first = $this->service->record($this->device, CredentialType::Nfc, 'BADGE-1');
    $second = $this->service->record($this->device, CredentialType::Nfc, 'BADGE-1');

    expect($first->direction)->toBe(PunchDirection::In)
        ->and($second->direction)->toBe(PunchDirection::Out);
});

test('an unknown identifier is rejected', function () {
    $this->service->record($this->device, CredentialType::Nfc, 'NOPE');
})->throws(UnknownCredentialException::class);

test('an inactive credential is rejected', function () {
    $this->credential->update(['is_active' => false]);

    $this->service->record($this->device, CredentialType::Nfc, 'BADGE-1');
})->throws(UnknownCredentialException::class);

test('a credential from another company is rejected on this device', function () {
    $otherEmployee = Employee::factory()->create(); // different company
    PunchCredential::factory()->for($otherEmployee)->create([
        'type' => CredentialType::Nfc,
        'identifier' => 'FOREIGN',
    ]);

    $this->service->record($this->device, CredentialType::Nfc, 'FOREIGN');
})->throws(UnknownCredentialException::class);
