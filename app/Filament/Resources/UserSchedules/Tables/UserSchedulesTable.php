<?php

namespace App\Filament\Resources\UserSchedules\Tables;

use App\Enums\ScheduleStatus;
use App\Models\UserSchedule;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('week_start_date', 'desc')
            ->columns([
                TextColumn::make('week_start_date')
                    ->label('Semaine du')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge(),
                TextColumn::make('shifts_count')
                    ->label('Créneaux')
                    ->counts('shifts')
                    ->badge(),
                TextColumn::make('published_at')
                    ->label('Publié le')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(ScheduleStatus::class),
            ])
            ->recordActions([
                Action::make('publish')
                    ->label('Publier')
                    ->icon(Heroicon::PaperAirplane)
                    ->color('success')
                    ->visible(fn (UserSchedule $record): bool => $record->status === ScheduleStatus::Draft)
                    ->requiresConfirmation()
                    ->action(fn (UserSchedule $record) => $record->update([
                        'status' => ScheduleStatus::Published,
                        'published_at' => now(),
                    ])),
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
