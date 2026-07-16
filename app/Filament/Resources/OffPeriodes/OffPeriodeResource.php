<?php

namespace App\Filament\Resources\OffPeriodes;

use App\Filament\Resources\OffPeriodes\Pages\CreateOffPeriode;
use App\Filament\Resources\OffPeriodes\Pages\EditOffPeriode;
use App\Filament\Resources\OffPeriodes\Pages\ListOffPeriodes;
use App\Filament\Resources\OffPeriodes\Schemas\OffPeriodeForm;
use App\Filament\Resources\OffPeriodes\Tables\OffPeriodesTable;
use App\Models\OffPeriode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OffPeriodeResource extends Resource
{
    protected static ?string $model = OffPeriode::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDateRange;

    protected static ?string $navigationLabel = 'Absences';

    protected static ?string $modelLabel = 'absence';

    protected static ?string $pluralModelLabel = 'absences';

    public static function form(Schema $schema): Schema
    {
        return OffPeriodeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OffPeriodesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOffPeriodes::route('/'),
            'create' => CreateOffPeriode::route('/create'),
            'edit' => EditOffPeriode::route('/{record}/edit'),
        ];
    }
}
