<?php

use App\Filament\Resources\Employees\EmployeeResource;
use App\Filament\Resources\Employees\Pages\ListEmployees;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->tenantA = Company::factory()->create();
    $this->tenantB = Company::factory()->create();

    $user = User::factory()->create();
    $this->tenantA->users()->attach($user);
    $this->tenantB->users()->attach($user);

    actingAs($user);
    Filament::setTenant($this->tenantA);
    Filament::setCurrentPanel('app');
    Filament::bootCurrentPanel();

    // bootCurrentPanel() registers a `creating` listener that stamps the current
    // tenant, so create each record while its own tenant is active.
    $this->mine = Employee::factory()->for($this->tenantA)->create();

    Filament::setTenant($this->tenantB);
    $this->theirs = Employee::factory()->create();
    Filament::setTenant($this->tenantA);
});

test('the list only shows the current tenant employees', function () {
    Livewire::test(ListEmployees::class)
        ->loadTable()
        ->assertCanSeeTableRecords([$this->mine])
        ->assertCanNotSeeTableRecords([$this->theirs]);
});

test('the resource query is scoped to the current tenant', function () {
    $ids = EmployeeResource::getEloquentQuery()->pluck('id');

    expect($ids)->toContain($this->mine->id)
        ->and($ids)->not->toContain($this->theirs->id);
});
