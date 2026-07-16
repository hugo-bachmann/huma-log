<?php

use App\Models\User;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertGuest;
use function Pest\Laravel\get;

function fakeGoogleUser(string $id, string $email, string $name = 'Google User'): void
{
    $socialiteUser = Mockery::mock(SocialiteUser::class);
    $socialiteUser->shouldReceive('getId')->andReturn($id);
    $socialiteUser->shouldReceive('getEmail')->andReturn($email);
    $socialiteUser->shouldReceive('getName')->andReturn($name);
    $socialiteUser->shouldReceive('getNickname')->andReturn(null);

    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->andReturn($socialiteUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
}

test('the redirect route hands off to Google', function () {
    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2'));
    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    get(route('auth.google.redirect'))
        ->assertRedirectContains('accounts.google.com');
});

test('a new Google user is created, verified and logged in', function () {
    fakeGoogleUser('google-123', 'new@example.com', 'New User');

    get(route('auth.google.callback'))->assertRedirect('/app');

    assertDatabaseHas(User::class, [
        'email' => 'new@example.com',
        'google_id' => 'google-123',
    ]);
    expect(User::where('email', 'new@example.com')->first()->email_verified_at)->not->toBeNull();
    assertAuthenticated();
});

test('an existing user with the same email is linked, not duplicated', function () {
    $user = User::factory()->create(['email' => 'existing@example.com', 'google_id' => null]);

    fakeGoogleUser('google-456', 'existing@example.com');

    get(route('auth.google.callback'))->assertRedirect('/app');

    expect(User::where('email', 'existing@example.com')->count())->toBe(1)
        ->and($user->fresh()->google_id)->toBe('google-456');
    assertAuthenticated();
});

test('a denied authorization redirects back to login and leaves the user a guest', function () {
    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->andThrow(new RuntimeException('denied'));
    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    get(route('auth.google.callback'))->assertRedirect(route('login'));

    assertGuest();
});
