<?php

use App\Models\Company;
use App\Models\User;

test('a company has many users', function () {
    $company = Company::factory()->create();
    $company->users()->attach(User::factory()->count(2)->create());

    expect($company->users)->toHaveCount(2);
});

test('a user belongs to many companies', function () {
    $user = User::factory()->create();
    $companies = Company::factory()->count(2)->create();
    $user->companies()->attach($companies);

    expect($user->companies)->toHaveCount(2)
        ->and($companies->first()->users->contains($user))->toBeTrue();
});
