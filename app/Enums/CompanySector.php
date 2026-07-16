<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CompanySector: string implements HasLabel
{
    case Commerce = 'commerce';
    case Restaurant = 'restaurant';
    case Artisan = 'artisan';
    case Service = 'service';
    case Care = 'soin';
    case Other = 'autre';

    public function getLabel(): string
    {
        return match ($this) {
            self::Commerce => 'Commerce',
            self::Restaurant => 'Restaurant',
            self::Artisan => 'Artisan',
            self::Service => 'Services',
            self::Care => 'Soin',
            self::Other => 'Autre',
        };
    }
}
