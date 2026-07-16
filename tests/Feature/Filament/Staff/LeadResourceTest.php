<?php

use App\Enums\LeadStatus;
use App\Filament\Staff\Resources\Leads\Pages\CreateLead;
use App\Filament\Staff\Resources\Leads\Pages\EditLead;
use App\Filament\Staff\Resources\Leads\Pages\ListLeads;
use App\Models\Lead;
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

test('the resource pages load', function () {
    $lead = Lead::factory()->create();

    Livewire::test(ListLeads::class)->assertOk();
    Livewire::test(CreateLead::class)->assertOk();
    Livewire::test(EditLead::class, ['record' => $lead->id])->assertOk();
});

test('the list shows leads', function () {
    $leads = Lead::factory()->count(3)->create();

    Livewire::test(ListLeads::class)
        ->loadTable()
        ->assertCanSeeTableRecords($leads);
});

test('a lead status can be advanced through the funnel', function () {
    $lead = Lead::factory()->create(['status' => LeadStatus::New]);

    Livewire::test(EditLead::class, ['record' => $lead->id])
        ->fillForm(['status' => LeadStatus::Qualified->value])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($lead->refresh()->status)->toBe(LeadStatus::Qualified);
});

test('the status filter narrows the list', function () {
    $new = Lead::factory()->create(['status' => LeadStatus::New]);
    $won = Lead::factory()->create(['status' => LeadStatus::Won]);

    Livewire::test(ListLeads::class)
        ->loadTable()
        ->filterTable('status', LeadStatus::Won->value)
        ->assertCanSeeTableRecords([$won])
        ->assertCanNotSeeTableRecords([$new]);
});

test('a lead can be created manually', function () {
    Livewire::test(CreateLead::class)
        ->fillForm([
            'name' => 'Karim Achour',
            'email' => 'k.achour@cafe-arts.fr',
            'company_name' => 'Café des Arts',
            'status' => LeadStatus::New->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Lead::class, [
        'email' => 'k.achour@cafe-arts.fr',
        'status' => LeadStatus::New->value,
    ]);
});
