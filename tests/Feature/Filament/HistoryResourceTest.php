<?php

use App\Enums\PunchDirection;
use App\Enums\PunchMethod;
use App\Filament\Resources\Histories\Pages\CreateHistory;
use App\Filament\Resources\Histories\Pages\ListHistories;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Punch;
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
    $employee = Employee::factory()->for($this->company)->create();
    Punch::factory()->for($employee)->create();

    Livewire::test(ListHistories::class)->assertOk();
    Livewire::test(CreateHistory::class)->assertOk();
});

test('a manual punch is created for the current tenant and stamped', function () {
    $employee = Employee::factory()->for($this->company)->create();

    Livewire::test(CreateHistory::class)
        ->fillForm([
            'employee_id' => $employee->id,
            'direction' => PunchDirection::In->value,
            'punched_at' => now()->subHours(2),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Punch::class, [
        'employee_id' => $employee->id,
        'company_id' => $this->company->id,
        'direction' => PunchDirection::In->value,
        'method' => PunchMethod::Manual->value,
        'created_by' => $this->user->id,
    ]);
});

test('the list only shows the current tenant punches', function () {
    $mineEmployee = Employee::factory()->for($this->company)->create();
    $mine = Punch::factory()->for($mineEmployee)->create();

    $otherCompany = Company::factory()->create();
    Filament::setTenant($otherCompany);
    $theirsEmployee = Employee::factory()->for($otherCompany)->create();
    $theirs = Punch::factory()->for($theirsEmployee)->create();
    Filament::setTenant($this->company);

    Livewire::test(ListHistories::class)
        ->loadTable()
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);
});
