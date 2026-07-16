<?php

use App\Enums\LeadStatus;
use App\Models\Lead;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('the landing page renders with its key sections', function () {
    get('/')
        ->assertOk()
        ->assertSee('compté juste', escape: false)
        ->assertSee('Tarifs')
        ->assertSee('Des questions ? On y répond.', escape: false);
});

test('the contact form creates a lead sourced from the landing', function () {
    post(route('contact.store'), [
        'name' => 'Karim Achour',
        'email' => 'k.achour@cafe-arts.fr',
        'company_name' => 'Café des Arts',
        'size' => '11-30',
        'message' => 'Intéressé pour notre ouverture.',
    ])
        ->assertRedirect()
        ->assertSessionHas('lead_sent', true);

    expect(Lead::where('email', 'k.achour@cafe-arts.fr')->first())
        ->not->toBeNull()
        ->source->toBe('Landing')
        ->status->toBe(LeadStatus::New)
        ->company_name->toBe('Café des Arts');
});

test('the contact form requires a name and a valid email', function () {
    post(route('contact.store'), ['email' => 'not-an-email'])
        ->assertSessionHasErrors(['name', 'email']);

    expect(Lead::count())->toBe(0);
});
