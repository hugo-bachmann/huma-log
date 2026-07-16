<?php

namespace App\Filament\Pages\Tenancy;

use App\Enums\CompanyRole;
use App\Enums\CompanySector;
use App\Models\Company;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class RegisterCompany extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Nouvel établissement';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Entreprise')
                        ->icon('heroicon-o-building-office-2')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nom de l\'entreprise')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                            TextInput::make('slug')
                                ->label('Adresse de votre espace')
                                ->prefix('app.huma-log.fr/')
                                ->required()
                                ->maxLength(32)
                                ->alphaDash()
                                ->unique(Company::class, 'slug'),
                            TextInput::make('email')
                                ->label('Email de contact')
                                ->email()
                                ->required()
                                ->maxLength(255),
                        ]),
                    Step::make('Détails')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->schema([
                            Select::make('sector')
                                ->label('Secteur d\'activité')
                                ->options(CompanySector::class)
                                ->required(),
                            Select::make('size')
                                ->label('Taille de l\'équipe')
                                ->options([
                                    '1-10' => '1 à 10 personnes',
                                    '11-30' => '11 à 30 personnes',
                                    '31-50' => '31 à 50 personnes',
                                    '50+' => 'Plus de 50 personnes',
                                ])
                                ->required(),
                        ]),
                    Step::make('Équipe')
                        ->icon('heroicon-o-users')
                        ->schema([
                            Repeater::make('invitations')
                                ->label('Inviter des collaborateurs')
                                ->helperText('Optionnel — ils recevront un email pour rejoindre votre espace.')
                                ->schema([
                                    TextInput::make('email')
                                        ->label('Email')
                                        ->email()
                                        ->required()
                                        ->distinct(),
                                    Select::make('role')
                                        ->label('Rôle')
                                        ->options(CompanyRole::class)
                                        ->default(CompanyRole::Member)
                                        ->required(),
                                ])
                                ->addActionLabel('Ajouter un collaborateur')
                                ->defaultItems(0),
                        ]),
                    Step::make('Lancement')
                        ->icon('heroicon-o-rocket-launch')
                        ->schema([
                            TextInput::make('confirm')
                                ->label('Votre espace est prêt')
                                ->helperText('Vérifiez les informations puis créez votre espace.')
                                ->disabled()
                                ->dehydrated(false)
                                ->placeholder('Cliquez sur « Créer l\'espace » pour terminer.'),
                        ]),
                ]),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Company
    {
        $invitations = $data['invitations'] ?? [];
        unset($data['invitations']);

        $company = Company::create($data);
        $company->users()->attach(auth()->user(), ['role' => CompanyRole::Admin->value]);

        foreach ($invitations as $invitation) {
            $company->invitations()->create([
                'email' => $invitation['email'],
                'role' => $invitation['role'],
            ]);
        }

        return $company;
    }
}
