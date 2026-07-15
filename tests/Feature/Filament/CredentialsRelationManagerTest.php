<?php

use App\Enums\CredentialType;
use App\Filament\Resources\Employees\Pages\EditEmployee;
use App\Filament\Resources\Employees\RelationManagers\CredentialsRelationManager;
use App\Models\Company;
use App\Models\Employee;
use App\Models\PunchCredential;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
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

    $this->employee = Employee::factory()->for($this->company)->create();
});

function credentialsManager(Employee $employee)
{
    return Livewire::test(CredentialsRelationManager::class, [
        'ownerRecord' => $employee,
        'pageClass' => EditEmployee::class,
    ]);
}

test('the relation manager lists the employee credentials', function () {
    $credential = PunchCredential::factory()->for($this->employee)->create();

    credentialsManager($this->employee)
        ->assertOk()
        ->assertCanSeeTableRecords([$credential]);
});

test('a credential can be created for the employee', function () {
    credentialsManager($this->employee)
        ->callAction(TestAction::make('create')->table(), data: [
            'type' => CredentialType::Nfc->value,
            'identifier' => 'AA:BB:CC:DD',
            'is_active' => true,
        ])
        ->assertHasNoActionErrors();

    expect($this->employee->credentials()->where('identifier', 'AA:BB:CC:DD')->exists())->toBeTrue();
});

test('a duplicate identifier of the same type is rejected', function () {
    PunchCredential::factory()->for($this->employee)->create([
        'type' => CredentialType::Nfc,
        'identifier' => 'DUP-1',
    ]);

    credentialsManager($this->employee)
        ->callAction(TestAction::make('create')->table(), data: [
            'type' => CredentialType::Nfc->value,
            'identifier' => 'DUP-1',
            'is_active' => true,
        ])
        ->assertHasActionErrors(['identifier']);
});
