<?php

namespace App\Filament\Resources\OffPeriodes\Tables;

use App\Enums\OffPeriodeStatus;
use App\Enums\OffPeriodeType;
use App\Models\Employee;
use App\Models\OffPeriode;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OffPeriodesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('starts_on', 'desc')
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('Employé')
                    ->formatStateUsing(fn ($record) => "{$record->employee->first_name} {$record->employee->last_name}")
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),
                TextColumn::make('starts_on')
                    ->label('Période')
                    ->formatStateUsing(fn ($record) => $record->starts_on->format('d/m/Y').' → '.$record->ends_on->format('d/m/Y'))
                    ->sortable(),
                TextColumn::make('working_days')
                    ->label('Jours ouvrés')
                    ->suffix(' j'),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge(),
                TextColumn::make('reason')
                    ->label('Motif')
                    ->limit(40)
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(OffPeriodeStatus::class),
                SelectFilter::make('type')
                    ->label('Type')
                    ->options(OffPeriodeType::class),
                SelectFilter::make('employee_id')
                    ->label('Employé')
                    ->relationship(
                        'employee',
                        'first_name',
                        fn (Builder $query) => $query->where('company_id', Filament::getTenant()->id),
                    )
                    ->getOptionLabelFromRecordUsing(fn (Employee $record) => "{$record->first_name} {$record->last_name}")
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approuver')
                    ->icon(Heroicon::Check)
                    ->color('success')
                    ->visible(fn (OffPeriode $record): bool => $record->status === OffPeriodeStatus::Pending)
                    ->requiresConfirmation()
                    ->action(fn (OffPeriode $record) => $record->update([
                        'status' => OffPeriodeStatus::Approved,
                        'decided_by' => auth()->id(),
                        'decided_at' => now(),
                    ])),
                Action::make('reject')
                    ->label('Refuser')
                    ->icon(Heroicon::XMark)
                    ->color('danger')
                    ->visible(fn (OffPeriode $record): bool => $record->status === OffPeriodeStatus::Pending)
                    ->requiresConfirmation()
                    ->action(fn (OffPeriode $record) => $record->update([
                        'status' => OffPeriodeStatus::Rejected,
                        'decided_by' => auth()->id(),
                        'decided_at' => now(),
                    ])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
