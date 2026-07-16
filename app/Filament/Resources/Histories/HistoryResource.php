<?php

namespace App\Filament\Resources\Histories;

use App\Filament\Resources\Histories\Pages\CreateHistory;
use App\Filament\Resources\Histories\Pages\ListHistories;
use App\Filament\Resources\Histories\Schemas\HistoryForm;
use App\Filament\Resources\Histories\Tables\HistoriesTable;
use App\Models\Punch;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Historique des pointages. Les Punch sont append-only (chaîne de hachage
 * anti-fraude) : on peut consulter et saisir manuellement, mais jamais
 * modifier ni supprimer — une correction est un nouveau pointage.
 */
class HistoryResource extends Resource
{
    protected static ?string $model = Punch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Historique';

    protected static ?string $modelLabel = 'pointage';

    protected static ?string $pluralModelLabel = 'pointages';

    public static function form(Schema $schema): Schema
    {
        return HistoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HistoriesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHistories::route('/'),
            'create' => CreateHistory::route('/create'),
        ];
    }
}
