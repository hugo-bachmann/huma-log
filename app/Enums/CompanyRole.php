<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CompanyRole: string implements HasLabel
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Member = 'member';

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Manager => 'Manager',
            self::Member => 'Collaborateur',
        };
    }
}
