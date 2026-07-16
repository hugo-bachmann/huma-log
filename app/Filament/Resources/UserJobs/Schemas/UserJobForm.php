<?php

namespace App\Filament\Resources\UserJobs\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserJobForm
{
    public static function configure(Schema $schema): Schema
    {
        // company_id is set automatically by the panel tenant.
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),
                ColorPicker::make('color')
                    ->label('Couleur')
                    ->default('#2FA875')
                    ->required(),
                TextInput::make('description')
                    ->label('Description')
                    ->maxLength(255),
                TextInput::make('hourly_rate')
                    ->label('Tarif horaire (brut)')
                    ->helperText('Optionnel — utilisé pour les rapports.')
                    ->numeric()
                    ->minValue(0)
                    ->suffix('€ / h'),
            ]);
    }
}
