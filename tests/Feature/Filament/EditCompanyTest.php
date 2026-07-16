<?php

use App\Filament\Pages\Tenancy\EditCompany;
use App\Models\Company;
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

test('the settings page loads with the current tenant data', function () {
    Livewire::test(EditCompany::class)
        ->assertOk()
        ->assertFormSet([
            'name' => $this->company->name,
        ]);
});

test('saving updates the current tenant', function () {
    Livewire::test(EditCompany::class)
        ->fillForm([
            'name' => 'Boulangerie Marceau',
            'legal_name' => 'SARL Marceau & Fils',
            'siret' => '528 491 027 00018',
            'email' => 'contact@marceau.fr',
            'timezone' => 'Europe/Brussels',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Company::class, [
        'id' => $this->company->id,
        'name' => 'Boulangerie Marceau',
        'legal_name' => 'SARL Marceau & Fils',
        'timezone' => 'Europe/Brussels',
    ]);
});
