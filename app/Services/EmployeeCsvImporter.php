<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Employee;

/**
 * Import synchrone d'employés depuis un CSV, dans le contexte d'une entreprise.
 *
 * Colonnes attendues (ligne d'en-tête, ordre libre) :
 * first_name, last_name, email, phone, job_title.
 * first_name et last_name sont obligatoires ; email, s'il est présent, doit être valide.
 */
class EmployeeCsvImporter
{
    /**
     * @return array{created: int, errors: array<int, string>}
     */
    public function import(Company $company, string $path): array
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return ['created' => 0, 'errors' => ['Fichier illisible.']];
        }

        $header = fgetcsv($handle);

        if ($header === false) {
            fclose($handle);

            return ['created' => 0, 'errors' => ['Fichier vide.']];
        }

        $columns = array_flip(array_map(
            fn ($name) => strtolower(trim((string) $name)),
            $header,
        ));

        if (! isset($columns['first_name'], $columns['last_name'])) {
            fclose($handle);

            return ['created' => 0, 'errors' => ['Colonnes « first_name » et « last_name » requises.']];
        }

        $created = 0;
        $errors = [];
        $line = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            $value = fn (string $key): string => trim((string) ($row[$columns[$key] ?? -1] ?? ''));

            $firstName = $value('first_name');
            $lastName = $value('last_name');
            $email = $value('email');

            if ($firstName === '' || $lastName === '') {
                $errors[] = "Ligne {$line} : prénom et nom obligatoires.";

                continue;
            }

            if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Ligne {$line} : email invalide.";

                continue;
            }

            Employee::create([
                'company_id' => $company->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email ?: null,
                'phone' => $value('phone') ?: null,
                'job_title' => $value('job_title') ?: null,
                'is_active' => true,
            ]);

            $created++;
        }

        fclose($handle);

        return ['created' => $created, 'errors' => $errors];
    }
}
