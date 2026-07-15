<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use App\Enums\CredentialType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class CredentialsRelationManager extends RelationManager
{
    protected static string $relationship = 'credentials';

    protected static ?string $title = 'Moyens de pointage';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label('Type')
                    ->options(CredentialType::class)
                    ->required(),
                TextInput::make('identifier')
                    ->label('Identifiant')
                    ->helperText('UID de la carte NFC, token du QR code, ou code PIN.')
                    ->required()
                    ->maxLength(255)
                    // Mirrors the DB unique(type, identifier) constraint.
                    ->unique(
                        modifyRuleUsing: fn (Unique $rule, callable $get) => $rule->where('type', $get('type')),
                        ignoreRecord: true,
                    ),
                TextInput::make('label')
                    ->label('Libellé')
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('Actif')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('identifier')
            ->columns([
                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),
                TextColumn::make('identifier')
                    ->label('Identifiant')
                    ->searchable(),
                TextColumn::make('label')
                    ->label('Libellé')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
                TextColumn::make('last_used_at')
                    ->label('Dernière utilisation')
                    ->since()
                    ->placeholder('Jamais'),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
