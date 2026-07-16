<?php

use App\Enums\OffPeriodeStatus;
use App\Enums\OffPeriodeType;
use App\Filament\Resources\OffPeriodes\Pages\CreateOffPeriode;
use App\Filament\Resources\OffPeriodes\Pages\EditOffPeriode;
use App\Filament\Resources\OffPeriodes\Pages\ListOffPeriodes;
use App\Models\Company;
use App\Models\Employee;
use App\Models\OffPeriode;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->user = User::factory()->create();
    $this->company->users()->attach($this->user);

    actingAs($this->user);
    Filament::setTenant($this->company);
    Filament::setCurrentPanel('app');
    Filament::bootCurrentPanel();
});

test('the resource pages load', function () {
    $off = OffPeriode::factory()->for($this->company)->create();

    Livewire::test(ListOffPeriodes::class)->assertOk();
    Livewire::test(CreateOffPeriode::class)->assertOk();
    Livewire::test(EditOffPeriode::class, ['record' => $off->id])->assertOk();
});

test('creating an absence assigns it to the current tenant and defaults to pending', function () {
    $employee = Employee::factory()->for($this->company)->create();

    Livewire::test(CreateOffPeriode::class)
        ->fillForm([
            'employee_id' => $employee->id,
            'type' => OffPeriodeType::PaidLeave->value,
            'starts_on' => now()->addWeek()->toDateString(),
            'ends_on' => now()->addWeek()->addDays(3)->toDateString(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(OffPeriode::class, [
        'employee_id' => $employee->id,
        'company_id' => $this->company->id,
        'status' => OffPeriodeStatus::Pending->value,
    ]);
});

test('the list only shows the current tenant absences', function () {
    $mine = OffPeriode::factory()->for($this->company)->create();

    $otherCompany = Company::factory()->create();
    Filament::setTenant($otherCompany);
    $theirs = OffPeriode::factory()->for($otherCompany)->create();
    Filament::setTenant($this->company);

    Livewire::test(ListOffPeriodes::class)
        ->loadTable()
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);
});

test('approving a pending absence stamps the decision', function () {
    $off = OffPeriode::factory()->for($this->company)->create(['status' => OffPeriodeStatus::Pending]);

    Livewire::test(ListOffPeriodes::class)
        ->callTableAction('approve', $off);

    $off->refresh();
    expect($off->status)->toBe(OffPeriodeStatus::Approved)
        ->and($off->decided_by)->toBe($this->user->id)
        ->and($off->decided_at)->not->toBeNull();
});

test('rejecting a pending absence stamps the decision', function () {
    $off = OffPeriode::factory()->for($this->company)->create(['status' => OffPeriodeStatus::Pending]);

    Livewire::test(ListOffPeriodes::class)
        ->callTableAction('reject', $off);

    expect($off->refresh()->status)->toBe(OffPeriodeStatus::Rejected);
});

test('working days ignore weekends', function () {
    // Monday to Sunday inclusive → 5 working days.
    $off = OffPeriode::factory()->for($this->company)->create([
        'starts_on' => '2026-05-11', // Monday
        'ends_on' => '2026-05-17',   // Sunday
    ]);

    expect($off->working_days)->toBe(5);
});
