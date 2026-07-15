<?php

namespace Database\Seeders;

use App\Enums\CredentialType;
use App\Models\Company;
use App\Models\Employee;
use App\Models\PunchCredential;
use App\Models\PunchDevice;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $company = Company::factory()->create(['name' => 'Resto Démo']);

        $user = User::factory()->create([
            'name' => 'Admin Démo',
            'email' => 'admin@demo.test',
        ]);
        $company->users()->attach($user);

        $employees = Employee::factory()->count(3)->for($company)->create();
        foreach ($employees as $index => $employee) {
            PunchCredential::factory()->for($employee)->create([
                'type' => CredentialType::Pin,
                'identifier' => (string) (1000 + $index),
                'label' => 'Code PIN',
            ]);
        }

        PunchDevice::factory()->for($company)->create([
            'name' => 'Borne entrée',
            'token' => 'demo-borne',
        ]);
        PunchDevice::factory()->for($company)->create(['name' => 'Borne cuisine']);

        $this->command->info('Kiosque : /kiosk/demo-borne — codes PIN : 1000, 1001, 1002');
    }
}
