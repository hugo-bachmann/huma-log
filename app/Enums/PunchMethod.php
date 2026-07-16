<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PunchMethod: string implements HasLabel
{
    case Nfc = 'nfc';
    case Qr = 'qr';
    case Pin = 'pin';
    case Manual = 'manual';

    public function getLabel(): string
    {
        return match ($this) {
            self::Nfc => 'NFC',
            self::Qr => 'QR code',
            self::Pin => 'Code PIN',
            self::Manual => 'Saisie manuelle',
        };
    }
}
