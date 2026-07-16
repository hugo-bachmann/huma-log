<?php

namespace App\Filament\Resources\OffPeriodes\Schemas;

use App\Enums\OffPeriodeType;
use App\Models\Employee;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class OffPeriodeForm
{
    /**
     * company_id est posé par le tenant. Le statut évolue via les actions
     * Approuver / Refuser (qui horodatent la décision), pas dans ce formulaire.
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
                Select::make('type')
                    ->label('Type d\'absence')
                    ->options(OffPeriodeType::class)
                    ->default(OffPeriodeType::PaidLeave)
                    ->required(),
                DatePicker::make('starts_on')
                    ->label('Du')
                    ->required(),
                DatePicker::make('ends_on')
                    ->label('Au')
                    ->afterOrEqual('starts_on')
                    ->required(),
                Textarea::make('reason')
                    ->label('Motif')
                    ->maxLength(1000)
                    ->columnSpanFull(),
                Textarea::make('internal_note')
                    ->label('Note interne')
                    ->helperText('Non visible par l\'employé.')
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]);
    }
}
