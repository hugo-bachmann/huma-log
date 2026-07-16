<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PunchDirection: string implements HasColor, HasLabel
{
    case In = 'in';
    case Out = 'out';

    public function getLabel(): string
    {
        return match ($this) {
            self::In => 'Entrée',
            self::Out => 'Sortie',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::In => 'success',
            self::Out => 'gray',
        };
    }
}
