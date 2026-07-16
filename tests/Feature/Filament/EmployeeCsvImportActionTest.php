<?php

use App\Filament\Resources\Employees\Pages\ListEmployees;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\UploadedFile;
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

test('importing a CSV creates employees for the current tenant', function () {
    $csv = implode("\n", [
        'first_name,last_name,email',
        'Marc,Dubois,marc@example.com',
        'Léa,Bernard,lea@example.com',
    ]);

    $file = UploadedFile::fake()->createWithContent('employees.csv', $csv);

    Livewire::test(ListEmployees::class)
        ->callAction('import', data: ['file' => $file])
        ->assertHasNoActionErrors();

    expect(Employee::where('company_id', $this->company->id)->count())->toBe(2);
});
