<?php

use App\Enums\PunchDirection;
use App\Filament\Widgets\LatestPunchesWidget;
use App\Filament\Widgets\PunchesChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Punch;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->user = User::factory()->create();
    $this->company->users()->attach($this->user);

    actingAs($this->user);
    Filament::setTenant($this->company);
    Filament::setCurrentPanel('app');
    Filament::bootCurrentPanel();
});

test('the widgets render', function () {
    Livewire::test(StatsOverviewWidget::class)->assertOk();
    Livewire::test(PunchesChartWidget::class)->assertOk();
    Livewire::test(LatestPunchesWidget::class)->assertOk();
});

test('the latest punches table only shows the current tenant punches', function () {
    $employee = Employee::factory()->for($this->company)->create();
    $mine = Punch::factory()->for($employee)->create(['direction' => PunchDirection::In]);

    // bootCurrentPanel() stamps the active tenant on every created record, so the
    // other tenant's punch must be created while that tenant is active.
    $otherCompany = Company::factory()->create();
    Filament::setTenant($otherCompany);
    $otherEmployee = Employee::factory()->for($otherCompany)->create();
    $theirs = Punch::factory()->for($otherEmployee)->create();
    Filament::setTenant($this->company);

    Livewire::test(LatestPunchesWidget::class)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);
});
