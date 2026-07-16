<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\EditTenantProfile;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditCompany extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Paramètres entreprise';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identité de l\'entreprise')
                    ->description('Nom et coordonnées affichés sur les rapports et invitations.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom commercial')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('legal_name')
                            ->label('Raison sociale')
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
                        TextInput::make('phone')
                            ->label('Téléphone')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email de contact')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ]),
                Section::make('Préférences')
                    ->schema([
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
