<?php

namespace App\Filament\Resources\PunchDevices;

use App\Filament\Resources\PunchDevices\Pages\CreatePunchDevice;
use App\Filament\Resources\PunchDevices\Pages\EditPunchDevice;
use App\Filament\Resources\PunchDevices\Pages\ListPunchDevices;
use App\Filament\Resources\PunchDevices\Schemas\PunchDeviceForm;
use App\Filament\Resources\PunchDevices\Tables\PunchDevicesTable;
use App\Models\PunchDevice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PunchDeviceResource extends Resource
{
    protected static ?string $model = PunchDevice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PunchDeviceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PunchDevicesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPunchDevices::route('/'),
            'create' => CreatePunchDevice::route('/create'),
            'edit' => EditPunchDevice::route('/{record}/edit'),
        ];
    }
}
