<?php

namespace App\Filament\Resources\UserSchedules\Schemas;

use App\Enums\ScheduleStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class UserScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        // company_id is set automatically by the panel tenant.
        return $schema
            ->components([
                DatePicker::make('week_start_date')
                    ->label('Semaine du (lundi)')
                    ->firstDayOfWeek(1)
                    ->required(),
                Select::make('status')
                    ->label('Statut')
                    ->options(ScheduleStatus::class)
                    ->default(ScheduleStatus::Draft)
                    ->required(),
            ]);
    }
}
