<?php

namespace App\Filament\Resources\UserSchedules\RelationManagers;

use App\Models\Employee;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShiftsRelationManager extends RelationManager
{
    protected static string $relationship = 'shifts';

    protected static ?string $title = 'Créneaux';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->label('Employé')
                    ->relationship(
                        'employee',
                        'first_name',
                        fn (Builder $query) => $query->where('company_id', Filament::getTenant()->id),
                    )
                    ->getOptionLabelFromRecordUsing(fn (Employee $record) => "{$record->first_name} {$record->last_name}")
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('user_job_id')
                    ->label('Poste')
                    ->relationship(
                        'userJob',
                        'name',
                        fn (Builder $query) => $query->where('company_id', Filament::getTenant()->id),
                    )
                    ->searchable()
                    ->preload(),
                DateTimePicker::make('starts_at')
                    ->label('Début')
                    ->seconds(false)
                    ->required(),
                DateTimePicker::make('ends_at')
                    ->label('Fin')
                    ->seconds(false)
                    ->after('starts_at')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('starts_at')
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('Employé')
                    ->formatStateUsing(fn ($record) => "{$record->employee->first_name} {$record->employee->last_name}")
                    ->searchable(['first_name', 'last_name']),
                TextColumn::make('userJob.name')
                    ->label('Poste')
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('starts_at')
                    ->label('Début')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Fin')
                    ->dateTime('d/m/Y H:i'),
                TextColumn::make('duration_hours')
                    ->label('Durée')
                    ->suffix(' h'),
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
