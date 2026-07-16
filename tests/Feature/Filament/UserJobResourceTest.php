<?php

use App\Filament\Resources\UserJobs\Pages\ManageUserJobs;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use App\Models\UserJob;
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

test('the manage page loads', function () {
    UserJob::factory()->for($this->company)->create();

    Livewire::test(ManageUserJobs::class)->assertOk();
});

test('creating a job assigns it to the current tenant', function () {
    Livewire::test(ManageUserJobs::class)
        ->callAction('create', data: [
            'name' => 'Boulanger',
            'color' => '#2FA875',
            'hourly_rate' => 13.50,
        ])
        ->assertHasNoActionErrors();

    assertDatabaseHas(UserJob::class, [
        'name' => 'Boulanger',
        'company_id' => $this->company->id,
        'hourly_rate' => 13.50,
    ]);
});

test('the list only shows the current tenant jobs', function () {
    $mine = UserJob::factory()->for($this->company)->create();

    $otherCompany = Company::factory()->create();
    Filament::setTenant($otherCompany);
    $theirs = UserJob::factory()->for($otherCompany)->create();
    Filament::setTenant($this->company);

    Livewire::test(ManageUserJobs::class)
        ->loadTable()
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);
});

test('the employees count reflects assigned employees', function () {
    $job = UserJob::factory()->for($this->company)->create();
    Employee::factory()->for($this->company)->for($job, 'userJob')->count(2)->create();

    Livewire::test(ManageUserJobs::class)
        ->loadTable()
        ->assertTableColumnStateSet('employees_count', 2, $job);
});
