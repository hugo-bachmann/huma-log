<?php

namespace Database\Seeders;

use App\Enums\CompanyRole;
use App\Enums\CredentialType;
use App\Enums\LeadStatus;
use App\Enums\PunchDirection;
use App\Enums\PunchMethod;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\Punch;
use App\Models\PunchCredential;
use App\Models\PunchDevice;
use App\Models\User;
use App\Models\UserJob;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Pas de WithoutModelEvents : les Punch ont besoin de leur événement
    // `creating` pour calculer la chaîne de hash (colonne `hash` NOT NULL).

    public function run(): void
    {
        // --- Compte admin + établissement (panel /app) ---
        $company = Company::factory()->create([
            'name' => 'Boulangerie Démo',
            'slug' => 'boulangerie-demo',
            'email' => 'contact@boulangerie-demo.test',
        ]);

        $admin = User::factory()->create([
            'name' => 'Admin Démo',
            'email' => 'admin@huma-log.test',
            'password' => 'password',
        ]);
        $company->users()->attach($admin, ['role' => CompanyRole::Admin->value]);

        // --- Compte staff (panel /staff, super-admin) ---
        User::factory()->staff()->create([
            'name' => 'Staff Huma·log',
            'email' => 'staff@huma-log.test',
            'password' => 'password',
        ]);

        // --- Postes + employés ---
        $baker = UserJob::factory()->for($company)->create(['name' => 'Boulanger', 'color' => '#2FA875', 'hourly_rate' => 13.50]);
        $cashier = UserJob::factory()->for($company)->create(['name' => 'Caisse', 'color' => '#E0A11F', 'hourly_rate' => 12.40]);

        $employees = collect([
            ['Marc', 'Dubois', $baker],
            ['Léa', 'Bernard', $cashier],
            ['Karim', 'Yacef', $baker],
        ])->map(fn ($e) => Employee::factory()->for($company)->for($e[2], 'userJob')->create([
            'first_name' => $e[0],
            'last_name' => $e[1],
            'email' => strtolower($e[0]).'@boulangerie-demo.test',
        ]));

        foreach ($employees as $index => $employee) {
            PunchCredential::factory()->for($employee)->create([
                'type' => CredentialType::Pin,
                'identifier' => (string) (1000 + $index),
                'label' => 'Code PIN',
            ]);
        }

        // --- Pointages (paires entrée/sortie sur les 5 derniers jours) ---
        foreach (range(4, 0) as $daysAgo) {
            foreach ($employees as $employee) {
                $in = now()->subDays($daysAgo)->setTime(8, 0);
                Punch::create([
                    'company_id' => $company->id,
                    'employee_id' => $employee->id,
                    'direction' => PunchDirection::In,
                    'method' => PunchMethod::Manual,
                    'punched_at' => $in,
                ]);
                Punch::create([
                    'company_id' => $company->id,
                    'employee_id' => $employee->id,
                    'direction' => PunchDirection::Out,
                    'method' => PunchMethod::Manual,
                    'punched_at' => $in->copy()->addHours(8),
                ]);
            }
        }

        // --- Bornes de pointage ---
        PunchDevice::factory()->for($company)->create(['name' => 'Borne entrée', 'token' => 'demo-borne']);
        PunchDevice::factory()->for($company)->create(['name' => 'Borne cuisine']);

        // --- Quelques leads pour le CRM staff ---
        Lead::factory()->create(['name' => 'Karim Achour', 'email' => 'k.achour@cafe-arts.test', 'company_name' => 'Café des Arts', 'source' => 'Landing', 'status' => LeadStatus::New]);
        Lead::factory()->create(['name' => 'Maïté Dorel', 'email' => 'maite@pressing.test', 'company_name' => 'Pressing Dorel', 'source' => 'Référencement', 'status' => LeadStatus::Contacted]);

        $this->command->info('Admin : admin@huma-log.test / password  (→ /app)');
        $this->command->info('Staff : staff@huma-log.test / password  (→ /staff)');
        $this->command->info('Kiosque : /kiosk/demo-borne — codes PIN : 1000, 1001, 1002');
    }
}
