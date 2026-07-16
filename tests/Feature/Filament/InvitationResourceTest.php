<?php

use App\Enums\CompanyRole;
use App\Filament\Resources\Invitations\Pages\ManageInvitations;
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
    Mail::fake();

    $this->company = Company::factory()->create();
    $this->user = User::factory()->create();
    $this->company->users()->attach($this->user);

    actingAs($this->user);
    Filament::setTenant($this->company);
    Filament::setCurrentPanel('app');
    Filament::bootCurrentPanel();
});

test('the manage page loads', function () {
    Invitation::factory()->for($this->company)->create();

    Livewire::test(ManageInvitations::class)->assertOk();
});

test('creating an invitation stamps the tenant and sends the email', function () {
    Livewire::test(ManageInvitations::class)
        ->callAction('create', data: [
            'email' => 'newbie@example.com',
            'role' => CompanyRole::Manager->value,
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(Invitation::class, [
        'email' => 'newbie@example.com',
        'company_id' => $this->company->id,
        'role' => CompanyRole::Manager->value,
    ]);

    Mail::assertSent(TeamInvitation::class);
});

test('the list only shows the current tenant invitations', function () {
    $mine = Invitation::factory()->for($this->company)->create();

    $otherCompany = Company::factory()->create();
    Filament::setTenant($otherCompany);
    $theirs = Invitation::factory()->for($otherCompany)->create();
    Filament::setTenant($this->company);

    Livewire::test(ManageInvitations::class)
        ->loadTable()
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);
});
