<?php

namespace App\Filament\Staff\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->weight('medium')
                    ->description(fn (User $record) => $record->email)
                    ->searchable(['name', 'email']),
                IconColumn::make('is_staff')
                    ->label('Staff')
                    ->boolean(),
                IconColumn::make('two_factor_confirmed_at')
                    ->label('2FA')
                    ->boolean(),
                TextColumn::make('companies_count')
                    ->label('Entreprises')
                    ->counts('companies')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_staff')
                    ->label('Staff'),
            ])
            ->recordActions([
                Action::make('revokeTwoFactor')
                    ->label('Révoquer 2FA')
                    ->icon('heroicon-o-shield-exclamation')
                    ->color('danger')
                    ->visible(fn (User $record): bool => $record->two_factor_confirmed_at !== null)
                    ->requiresConfirmation()
                    ->action(fn (User $record) => $record->forceFill([
                        'two_factor_secret' => null,
                        'two_factor_recovery_codes' => null,
                        'two_factor_confirmed_at' => null,
                    ])->save()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
