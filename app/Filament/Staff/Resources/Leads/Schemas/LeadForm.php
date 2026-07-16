<?php

namespace App\Filament\Staff\Resources\Leads\Schemas;

use App\Enums\LeadStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('company_name')
                    ->label('Entreprise')
                    ->maxLength(255),
                Select::make('size')
                    ->label('Effectif')
                    ->options([
                        '1-10' => '1-10',
                        '11-30' => '11-30',
                        '31-50' => '31-50',
                        '50+' => '50+',
                    ]),
                TextInput::make('source')
                    ->label('Source')
                    ->maxLength(255),
                Select::make('status')
                    ->label('Statut')
                    ->options(LeadStatus::class)
                    ->default(LeadStatus::New)
                    ->required(),
                Textarea::make('message')
                    ->label('Message')
                    ->maxLength(2000)
                    ->columnSpanFull(),
            ]);
    }
}
