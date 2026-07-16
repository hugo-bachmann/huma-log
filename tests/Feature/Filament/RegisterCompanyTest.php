<?php

use App\Enums\CompanyRole;
use App\Enums\CompanySector;
use App\Filament\Pages\Tenancy\RegisterCompany;
use App\Mail\TeamInvitation;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    $this->user = User::factory()->create();

    actingAs($this->user);
    Filament::setCurrentPanel('app');
    Filament::bootCurrentPanel();
});

test('the wizard provisions a company and attaches the current user', function () {
    Livewire::test(RegisterCompany::class)
        ->fillForm([
            'name' => 'Boulangerie Marceau',
            'slug' => 'marceau',
            'email' => 'contact@marceau.fr',
            'sector' => CompanySector::Restaurant->value,
            'size' => '11-30',
        ])
        ->call('register')
        ->assertHasNoFormErrors();

    $company = Company::where('slug', 'marceau')->first();

    expect($company)->not->toBeNull()
        ->and($company->sector)->toBe(CompanySector::Restaurant)
        ->and($company->size)->toBe('11-30');

    assertDatabaseHas('company_user', [
        'company_id' => $company->id,
        'user_id' => $this->user->id,
    ]);
});

test('the wizard team step creates invitations and emails them', function () {
    Mail::fake();

    Livewire::test(RegisterCompany::class)
        ->fillForm([
            'name' => 'Café des Arts',
            'slug' => 'cafe-des-arts',
            'email' => 'contact@cafe-arts.fr',
            'sector' => CompanySector::Restaurant->value,
            'size' => '1-10',
            'invitations' => [
                ['email' => 'lea@cafe-arts.fr', 'role' => CompanyRole::Manager->value],
                ['email' => 'karim@cafe-arts.fr', 'role' => CompanyRole::Member->value],
            ],
        ])
        ->call('register')
        ->assertHasNoFormErrors();

    $company = Company::where('slug', 'cafe-des-arts')->first();

    assertDatabaseHas(Invitation::class, [
        'company_id' => $company->id,
        'email' => 'lea@cafe-arts.fr',
        'role' => CompanyRole::Manager->value,
    ]);
    expect($company->invitations()->count())->toBe(2);
    Mail::assertSent(TeamInvitation::class, 2);
});

test('the space slug must be unique', function () {
    Company::factory()->create(['slug' => 'marceau']);

    Livewire::test(RegisterCompany::class)
        ->fillForm([
            'name' => 'Autre Marceau',
            'slug' => 'marceau',
            'email' => 'autre@marceau.fr',
            'sector' => CompanySector::Commerce->value,
            'size' => '1-10',
        ])
        ->call('register')
        ->assertHasFormErrors(['slug']);
});
