<?php

namespace App\Filament\Resources\PunchDevices\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PunchDeviceForm
{
    public static function configure(Schema $schema): Schema
    {
        // company_id is set automatically by the panel tenant.
        // last_seen_at is written by the device heartbeat, not here.
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('location')
                    ->maxLength(255),
                TextInput::make('token')
                    ->helperText('Clé d\'authentification de la borne, générée automatiquement.')
                    ->default(fn () => Str::random(40))
                    ->readOnly()
                    ->copyable()
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
