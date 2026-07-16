<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum OffPeriodeType: string implements HasLabel
{
    case PaidLeave = 'paid_leave';
    case Rtt = 'rtt';
    case Sick = 'sick';
    case Unpaid = 'unpaid';
    case Training = 'training';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::PaidLeave => 'Congés payés',
            self::Rtt => 'RTT',
            self::Sick => 'Maladie',
            self::Unpaid => 'Sans solde',
            self::Training => 'Formation',
            self::Other => 'Autre',
        };
    }
}
