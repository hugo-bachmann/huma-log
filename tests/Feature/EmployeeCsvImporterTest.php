<?php

use App\Models\Company;
use App\Models\Employee;
use App\Services\EmployeeCsvImporter;

function writeCsv(string $content): string
{
    $path = tempnam(sys_get_temp_dir(), 'emp').'.csv';
    file_put_contents($path, $content);

    return $path;
}

test('it imports valid rows and reports invalid ones', function () {
    $company = Company::factory()->create();

    $path = writeCsv(implode("\n", [
        'first_name,last_name,email,phone,job_title',
        'Marc,Dubois,marc@example.com,0102030405,Boulanger',
        'Léa,Bernard,,,Caisse',            // no email → valid
        'Jean,Petit,not-an-email,,',       // bad email → error
        ',,,,',                            // missing names → error
    ]));

    $result = (new EmployeeCsvImporter)->import($company, $path);

    expect($result['created'])->toBe(2)
        ->and($result['errors'])->toHaveCount(2);

    expect(Employee::where('company_id', $company->id)->count())->toBe(2)
        ->and(Employee::where('email', 'marc@example.com')->where('company_id', $company->id)->exists())->toBeTrue()
        ->and(Employee::where('first_name', 'Léa')->where('email', null)->exists())->toBeTrue();

    unlink($path);
});

test('column order is flexible and headers are matched by name', function () {
    $company = Company::factory()->create();

    $path = writeCsv(implode("\n", [
        'job_title,last_name,first_name',
        'Pâtissier,Yacef,Karim',
    ]));

    $result = (new EmployeeCsvImporter)->import($company, $path);

    expect($result['created'])->toBe(1);
    expect(Employee::where('first_name', 'Karim')->where('last_name', 'Yacef')->exists())->toBeTrue();

    unlink($path);
});

test('a file missing required columns is rejected', function () {
    $company = Company::factory()->create();

    $path = writeCsv("email,phone\nx@y.fr,0102");

    $result = (new EmployeeCsvImporter)->import($company, $path);

    expect($result['created'])->toBe(0)
        ->and($result['errors'])->toHaveCount(1);

    unlink($path);
});
