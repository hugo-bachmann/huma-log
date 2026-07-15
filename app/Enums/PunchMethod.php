<?php

namespace App\Enums;

enum PunchMethod: string
{
    case Nfc = 'nfc';
    case Qr = 'qr';
    case Pin = 'pin';
    case Manual = 'manual';
}
