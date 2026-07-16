<?php

use App\Enums\CompanyRole;
use App\Mail\TeamInvitation;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

beforeEach(function () {
    Mail::fake();
});

test('creating an invitation generates a token, an expiry and sends an email', function () {
    $invitation = Invitation::factory()->create(['token' => null, 'expires_at' => null]);

    expect($invitation->token)->not->toBeNull()
        ->and($invitation->expires_at->isFuture())->toBeTrue();

    Mail::assertSent(TeamInvitation::class, fn (TeamInvitation $mail) => $mail->hasTo($invitation->email));
});

test('accepting attaches the user to the company with the invited role', function () {
    $company = Company::factory()->create();
    $user = User::factory()->create(['email' => 'joiner@example.com']);
    $invitation = Invitation::factory()->for($company)->create([
        'email' => 'joiner@example.com',
        'role' => CompanyRole::Manager,
    ]);

    $invitation->accept($user);

    assertDatabaseHas('company_user', [
        'company_id' => $company->id,
        'user_id' => $user->id,
        'role' => CompanyRole::Manager->value,
    ]);
    expect($invitation->fresh()->accepted_at)->not->toBeNull();
});

test('a logged-in matching user accepts via the link', function () {
    $company = Company::factory()->create();
    $user = User::factory()->create(['email' => 'joiner@example.com']);
    $invitation = Invitation::factory()->for($company)->create(['email' => 'joiner@example.com']);

    actingAs($user)
        ->get(route('invitation.accept', $invitation->token))
        ->assertRedirect('/app');

    assertDatabaseHas('company_user', [
        'company_id' => $company->id,
        'user_id' => $user->id,
    ]);
});

test('a logged-in user with a different email is forbidden', function () {
    $invitation = Invitation::factory()->create(['email' => 'invited@example.com']);
    $other = User::factory()->create(['email' => 'someone-else@example.com']);

    actingAs($other)
        ->get(route('invitation.accept', $invitation->token))
        ->assertForbidden();
});

test('a guest is redirected to authenticate', function () {
    $invitation = Invitation::factory()->create();

    get(route('invitation.accept', $invitation->token))
        ->assertRedirect(route('register'));
});

test('an accepted or expired invitation is no longer valid', function () {
    $expired = Invitation::factory()->create(['expires_at' => now()->subDay()]);
    $user = User::factory()->create(['email' => $expired->email]);

    actingAs($user)
        ->get(route('invitation.accept', $expired->token))
        ->assertRedirect(route('login'));

    expect($expired->fresh()->accepted_at)->toBeNull();
});
