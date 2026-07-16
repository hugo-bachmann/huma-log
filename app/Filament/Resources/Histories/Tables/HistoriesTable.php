<?php

namespace App\Filament\Resources\Histories\Tables;

use App\Enums\PunchDirection;
use App\Enums\PunchMethod;
use App\Models\Employee;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HistoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('punched_at', 'desc')
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('Employé')
                    ->formatStateUsing(fn ($record) => "{$record->employee->first_name} {$record->employee->last_name}")
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                TextColumn::make('direction')
                    ->label('Type')
                    ->badge(),
                TextColumn::make('punched_at')
                    ->label('Date et heure')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('method')
                    ->label('Source')
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_suspect')
                    ->label('Anomalie')
                    ->boolean()
                    ->trueIcon(Heroicon::ExclamationTriangle)
                    ->trueColor('warning')
                    ->falseIcon(Heroicon::CheckCircle)
                    ->falseColor('success'),
                TextColumn::make('createdBy.name')
                    ->label('Saisi par')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('note')
                    ->label('Commentaire')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
                SelectFilter::make('direction')
                    ->label('Type')
                    ->options(PunchDirection::class),
                SelectFilter::make('method')
                    ->label('Source')
                    ->options(PunchMethod::class),
                TernaryFilter::make('is_suspect')
                    ->label('Anomalie'),
                Filter::make('punched_at')
                    ->schema([
                        DatePicker::make('from')->label('Du'),
                        DatePicker::make('until')->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('punched_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('punched_at', '<=', $date));
                    }),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
