<?php

use App\Enums\ScheduleStatus;
use App\Filament\Resources\UserSchedules\Pages\CreateUserSchedule;
use App\Filament\Resources\UserSchedules\Pages\EditUserSchedule;
use App\Filament\Resources\UserSchedules\Pages\ListUserSchedules;
use App\Filament\Resources\UserSchedules\RelationManagers\ShiftsRelationManager;
use App\Models\Company;
use App\Models\Employee;
use App\Models\ScheduleShift;
use App\Models\User;
use App\Models\UserSchedule;
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
    $schedule = UserSchedule::factory()->for($this->company)->create();

    Livewire::test(ListUserSchedules::class)->assertOk();
    Livewire::test(CreateUserSchedule::class)->assertOk();
    Livewire::test(EditUserSchedule::class, ['record' => $schedule->id])->assertOk();
});

test('creating a schedule assigns it to the current tenant', function () {
    Livewire::test(CreateUserSchedule::class)
        ->fillForm([
            'week_start_date' => now()->startOfWeek()->toDateString(),
            'status' => ScheduleStatus::Draft->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(UserSchedule::class, [
        'company_id' => $this->company->id,
        'status' => ScheduleStatus::Draft->value,
    ]);
});

test('the list only shows the current tenant schedules', function () {
    $mine = UserSchedule::factory()->for($this->company)->create();

    $otherCompany = Company::factory()->create();
    Filament::setTenant($otherCompany);
    $theirs = UserSchedule::factory()->for($otherCompany)->create();
    Filament::setTenant($this->company);

    Livewire::test(ListUserSchedules::class)
        ->loadTable()
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);
});

test('publishing a draft schedule sets status and timestamp', function () {
    $schedule = UserSchedule::factory()->for($this->company)->create(['status' => ScheduleStatus::Draft]);

    Livewire::test(ListUserSchedules::class)
        ->callTableAction('publish', $schedule);

    $schedule->refresh();
    expect($schedule->status)->toBe(ScheduleStatus::Published)
        ->and($schedule->published_at)->not->toBeNull();
});

test('a shift can be added to a schedule via the relation manager', function () {
    $schedule = UserSchedule::factory()->for($this->company)->create();
    $employee = Employee::factory()->for($this->company)->create();

    $start = now()->startOfWeek()->setTime(9, 0);

    Livewire::test(ShiftsRelationManager::class, [
        'ownerRecord' => $schedule,
        'pageClass' => EditUserSchedule::class,
    ])
        ->callTableAction('create', data: [
            'employee_id' => $employee->id,
            'starts_at' => $start->toDateTimeString(),
            'ends_at' => $start->copy()->addHours(8)->toDateTimeString(),
        ])
        ->assertHasNoTableActionErrors();

    assertDatabaseHas(ScheduleShift::class, [
        'user_schedule_id' => $schedule->id,
        'employee_id' => $employee->id,
    ]);
});
