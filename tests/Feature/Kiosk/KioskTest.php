<?php

use App\Enums\CredentialType;
use App\Enums\PunchDirection;
use App\Livewire\Kiosk;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Punch;
use App\Models\PunchCredential;
use App\Models\PunchDevice;
use Livewire\Livewire;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->device = PunchDevice::factory()->for($this->company)->create(['is_active' => true]);
    $this->employee = Employee::factory()->for($this->company)->create(['first_name' => 'Jean', 'last_name' => 'Dupont']);
    PunchCredential::factory()->for($this->employee)->create([
        'type' => CredentialType::Pin,
        'identifier' => '1234',
    ]);
});

test('the kiosk route loads for an active device', function () {
    $this->get(route('kiosk', $this->device->token))->assertOk();
});

test('the kiosk route 404s for an inactive device', function () {
    $this->device->update(['is_active' => false]);

    $this->get(route('kiosk', $this->device->token))->assertNotFound();
});

test('a valid scan records a punch and confirms to the employee', function () {
    Livewire::test(Kiosk::class, ['device' => $this->device])
        ->set('type', 'pin')
        ->set('identifier', '1234')
        ->call('punch')
        ->assertSet('feedback.ok', true)
        ->assertSee('Jean Dupont');

    expect(Punch::query()->where('employee_id', $this->employee->id)->where('direction', PunchDirection::In)->exists())
        ->toBeTrue();
});

test('an unknown code shows an error and records nothing', function () {
    Livewire::test(Kiosk::class, ['device' => $this->device])
        ->set('type', 'pin')
        ->set('identifier', '0000')
        ->call('punch')
        ->assertSet('feedback.ok', false);

    expect(Punch::query()->count())->toBe(0);
});
