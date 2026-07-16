<?php

namespace App\Filament\Resources\Histories\Schemas;

use App\Enums\PunchDirection;
use App\Models\Employee;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class HistoryForm
{
    /**
     * Saisie manuelle uniquement : company_id, method et created_by sont
     * posés dans CreateHistory. Pas d'édition (Punch append-only).
     */
    public static function configure(Schema $schema): Schema
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
                Select::make('direction')
                    ->label('Type')
                    ->options(PunchDirection::class)
                    ->default(PunchDirection::In)
                    ->required(),
                DateTimePicker::make('punched_at')
                    ->label('Date et heure')
                    ->seconds(false)
                    ->default(now())
                    ->required(),
                Textarea::make('note')
                    ->label('Commentaire')
                    ->helperText('Conservé dans l\'audit du pointage.')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }
}
