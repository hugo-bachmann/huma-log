<?php

namespace App\Filament\Widgets;

use App\Models\Punch;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestPunchesWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Derniers pointages';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Punch::query())
            ->modifyQueryUsing(
                fn (Builder $query): Builder => $query
                    ->where('company_id', Filament::getTenant()->id)
                    ->latest('punched_at')
            )
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('Employé')
                    ->formatStateUsing(fn ($record) => "{$record->employee->first_name} {$record->employee->last_name}"),
                TextColumn::make('direction')
                    ->label('Type')
                    ->badge(),
                TextColumn::make('punched_at')
                    ->label('Date et heure')
                    ->dateTime('d/m/Y H:i'),
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
            ])
            ->paginated([5]);
    }
}
