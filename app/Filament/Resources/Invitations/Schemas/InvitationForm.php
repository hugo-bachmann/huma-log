<?php

namespace App\Filament\Resources\Invitations\Schemas;

use App\Enums\CompanyRole;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class InvitationForm
{
    public static function configure(Schema $schema): Schema
    {
        // company_id est posé par le tenant.
        return $schema
            ->components([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    // Une seule invitation en attente par email et par entreprise.
                    ->unique(
                        modifyRuleUsing: fn (Unique $rule) => $rule->where('company_id', Filament::getTenant()->id),
                        ignoreRecord: true,
                    ),
                Select::make('role')
                    ->label('Rôle')
                    ->options(CompanyRole::class)
                    ->default(CompanyRole::Member)
                    ->required(),
            ]);
    }
}
