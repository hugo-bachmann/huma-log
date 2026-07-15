<?php

use App\Filament\Resources\PunchDevices\Pages\CreatePunchDevice;
use App\Filament\Resources\PunchDevices\Pages\EditPunchDevice;
use App\Filament\Resources\PunchDevices\Pages\ListPunchDevices;
use App\Models\Company;
use App\Models\PunchDevice;
use App\Models\User;
use Filament\Facades\Filament;

use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $user = User::factory()->create();
    $this->company->users()->attach($user);

    actingAs($user);
    Filament::setTenant($this->company);
    Filament::setCurrentPanel('app');
    Filament::bootCurrentPanel();
});

test('the resource pages load', function () {
    $device = PunchDevice::factory()->for($this->company)->create();

    Livewire::test(ListPunchDevices::class)->assertOk();
    Livewire::test(CreatePunchDevice::class)->assertOk();
    Livewire::test(EditPunchDevice::class, ['record' => $device->id])->assertOk();
});

test('creating a device assigns it to the current tenant with a token', function () {
    Livewire::test(CreatePunchDevice::class)
        ->fillForm([
            'name' => 'Borne entrée',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $device = PunchDevice::query()->where('name', 'Borne entrée')->sole();

    expect($device->company_id)->toBe($this->company->id)
        ->and($device->token)->not->toBeEmpty();
});
