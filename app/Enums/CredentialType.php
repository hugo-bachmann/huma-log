<?php

namespace App\Enums;

enum CredentialType: string
{
    case Nfc = 'nfc';
    case Qr = 'qr';
    case Pin = 'pin';
}
