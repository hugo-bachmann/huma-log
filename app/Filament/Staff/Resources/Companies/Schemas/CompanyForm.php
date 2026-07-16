<?php

namespace App\Filament\Staff\Resources\Companies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identité')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom commercial')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('legal_name')
                            ->label('Raison sociale')
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email de contact')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('siret')
                            ->label('SIRET')
                            ->maxLength(255),
                        TextInput::make('ape_code')
                            ->label('Code APE')
                            ->maxLength(255),
                        TextInput::make('address')
                            ->label('Adresse')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('timezone')
                            ->label('Fuseau horaire')
                            ->options([
                                'Europe/Paris' => 'Europe / Paris',
                                'Europe/Brussels' => 'Europe / Bruxelles',
                                'Europe/Luxembourg' => 'Europe / Luxembourg',
                                'Europe/Zurich' => 'Europe / Zurich',
                            ])
                            ->default('Europe/Paris')
                            ->required(),
                    ]),
            ]);
    }
}
