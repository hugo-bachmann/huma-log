<?php

use App\Enums\PunchDirection;
use App\Enums\PunchMethod;
use App\Filament\Pages\GenerateRecap;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Punch;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;

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

test('the page loads', function () {
    Livewire::test(GenerateRecap::class)->assertOk();
});

test('generating returns a PDF download', function () {
    $employee = Employee::factory()->for($this->company)->create();
    Punch::factory()->for($employee)->create([
        'direction' => PunchDirection::In,
        'method' => PunchMethod::Manual,
        'punched_at' => now()->startOfMonth()->addDays(2)->setTime(8, 0),
    ]);
    Punch::factory()->for($employee)->create([
        'direction' => PunchDirection::Out,
        'method' => PunchMethod::Manual,
        'punched_at' => now()->startOfMonth()->addDays(2)->setTime(16, 0),
    ]);

    $response = Livewire::test(GenerateRecap::class)->instance()->generate();

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
    expect($response->getContent())->toStartWith('%PDF');
});
