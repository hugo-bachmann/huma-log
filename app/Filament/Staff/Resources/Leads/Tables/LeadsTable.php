<?php

namespace App\Filament\Staff\Resources\Leads\Tables;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->weight('medium')
                    ->description(fn ($record) => $record->email)
                    ->searchable(['name', 'email']),
                TextColumn::make('company_name')
                    ->label('Entreprise')
                    ->searchable()
                    ->color('gray'),
                TextColumn::make('source')
                    ->label('Source')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Reçu')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(LeadStatus::class),
                SelectFilter::make('source')
                    ->label('Source')
                    ->options(fn () => Lead::query()
                        ->whereNotNull('source')
                        ->distinct()
                        ->pluck('source', 'source')
                        ->all()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
