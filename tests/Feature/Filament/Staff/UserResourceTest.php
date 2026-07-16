<?php

use App\Filament\Staff\Resources\Users\Pages\CreateUser;
use App\Filament\Staff\Resources\Users\Pages\EditUser;
use App\Filament\Staff\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->staff = User::factory()->staff()->create();

    actingAs($this->staff);
    Filament::setCurrentPanel('staff');
    Filament::bootCurrentPanel();
});

test('the resource pages load', function () {
    $user = User::factory()->create();

    Livewire::test(ListUsers::class)->assertOk();
    Livewire::test(CreateUser::class)->assertOk();
    Livewire::test(EditUser::class, ['record' => $user->id])->assertOk();
});

test('creating a user persists the staff flag and hashes the password', function () {
    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Sophie Marceau',
            'email' => 'sophie@huma-log.fr',
            'password' => 'super-secret',
            'is_staff' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $user = User::where('email', 'sophie@huma-log.fr')->first();

    expect($user->is_staff)->toBeTrue()
        ->and(Hash::check('super-secret', $user->password))->toBeTrue();
});

test('editing without a new password leaves it unchanged', function () {
    $user = User::factory()->create(['password' => Hash::make('original')]);

    Livewire::test(EditUser::class, ['record' => $user->id])
        ->fillForm([
            'name' => 'Nouveau Nom',
            'password' => '',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $user->refresh();
    expect($user->name)->toBe('Nouveau Nom')
        ->and(Hash::check('original', $user->password))->toBeTrue();
});

test('a staff user can revoke another user two-factor', function () {
    $user = User::factory()->withTwoFactor()->create();

    Livewire::test(ListUsers::class)
        ->callTableAction('revokeTwoFactor', $user);

    $user->refresh();
    expect($user->two_factor_confirmed_at)->toBeNull()
        ->and($user->two_factor_secret)->toBeNull();
});

test('the email must be unique', function () {
    User::factory()->create(['email' => 'taken@huma-log.fr']);

    Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Doublon',
            'email' => 'taken@huma-log.fr',
            'password' => 'password123',
        ])
        ->call('create')
        ->assertHasFormErrors(['email']);
});
