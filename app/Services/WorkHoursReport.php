<?php

namespace App\Services;

use App\Enums\PunchDirection;
use App\Models\Company;
use App\Models\Employee;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Compile les heures travaillées par employé sur une période, en appariant
 * chaque pointage d'entrée avec le pointage de sortie qui suit.
 *
 * Les pointages sont append-only : une entrée sans sortie (ou l'inverse) est
 * ignorée. Le découpage heures normales / supplémentaires n'est pas calculé ici.
 */
class WorkHoursReport
{
    public function __construct(
        public Company $company,
        public CarbonInterface $from,
        public CarbonInterface $to,
    ) {}

    /**
     * @return Collection<int, array{employee: Employee, minutes: int}>
     */
    public function perEmployee(): Collection
    {
        return $this->company
            ->employees()
            ->with(['punches' => fn ($query) => $query
                ->whereBetween('punched_at', [$this->from, $this->to])
                ->orderBy('punched_at'),
            ])
            ->get()
            ->map(fn (Employee $employee) => [
                'employee' => $employee,
                'minutes' => $this->minutesFor($employee),
            ])
            ->filter(fn (array $row) => $row['minutes'] > 0)
            ->values();
    }

    public function totalMinutes(): int
    {
        return (int) $this->perEmployee()->sum('minutes');
    }

    /**
     * Somme des minutes des paires entrée→sortie de l'employé.
     */
    private function minutesFor(Employee $employee): int
    {
        $minutes = 0;
        $openIn = null;

        foreach ($employee->punches as $punch) {
            if ($punch->direction === PunchDirection::In) {
                $openIn ??= $punch->punched_at;

                continue;
            }

            // Sortie : ne compte que si une entrée est ouverte.
            if ($openIn !== null) {
                $minutes += $openIn->diffInMinutes($punch->punched_at);
                $openIn = null;
            }
        }

        return $minutes;
    }

    /**
     * Formate des minutes en "H:MM" (ex. 148:30).
     */
    public static function formatMinutes(int $minutes): string
    {
        return sprintf('%d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
