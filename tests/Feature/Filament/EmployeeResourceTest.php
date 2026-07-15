<?php

use App\Filament\Resources\Employees\Pages\CreateEmployee;
use App\Filament\Resources\Employees\Pages\EditEmployee;
use App\Filament\Resources\Employees\Pages\ListEmployees;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Filament\Facades\Filament;

use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

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
    $employee = Employee::factory()->for($this->company)->create();

    Livewire::test(ListEmployees::class)->assertOk();
    Livewire::test(CreateEmployee::class)->assertOk();
    Livewire::test(EditEmployee::class, ['record' => $employee->id])->assertOk();
});

test('creating an employee assigns it to the current tenant', function () {
    Livewire::test(CreateEmployee::class)
        ->fillForm([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Employee::class, [
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
        'company_id' => $this->company->id,
    ]);
});
