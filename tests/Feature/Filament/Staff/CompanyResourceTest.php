<?php

use App\Filament\Staff\Resources\Companies\Pages\CreateCompany;
use App\Filament\Staff\Resources\Companies\Pages\EditCompany;
use App\Filament\Staff\Resources\Companies\Pages\ListCompanies;
use App\Models\Company;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->staff = User::factory()->staff()->create();

    actingAs($this->staff);
    Filament::setCurrentPanel('staff');
    Filament::bootCurrentPanel();
});

test('a staff user can access the staff panel', function () {
    $panel = Filament::getPanel('staff');

    expect($this->staff->canAccessPanel($panel))->toBeTrue();
});

test('a non-staff user cannot access the staff panel', function () {
    $regular = User::factory()->create();

    expect($regular->canAccessPanel(Filament::getPanel('staff')))->toBeFalse();
});

test('the company resource pages load', function () {
    $company = Company::factory()->create();

    Livewire::test(ListCompanies::class)->assertOk();
    Livewire::test(CreateCompany::class)->assertOk();
    Livewire::test(EditCompany::class, ['record' => $company->id])->assertOk();
});

test('the list shows every company globally (no tenant scope)', function () {
    $a = Company::factory()->create();
    $b = Company::factory()->create();

    Livewire::test(ListCompanies::class)
        ->loadTable()
        ->assertCanSeeTableRecords([$a, $b]);
});

test('a staff user can create a company', function () {
    Livewire::test(CreateCompany::class)
        ->fillForm([
            'name' => 'Boulangerie Marceau',
            'email' => 'contact@marceau.fr',
            'timezone' => 'Europe/Paris',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Company::class, [
        'name' => 'Boulangerie Marceau',
        'email' => 'contact@marceau.fr',
    ]);
});
