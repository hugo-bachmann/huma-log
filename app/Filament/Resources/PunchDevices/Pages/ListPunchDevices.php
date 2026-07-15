<?php

namespace App\Filament\Resources\PunchDevices\Pages;

use App\Filament\Resources\PunchDevices\PunchDeviceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPunchDevices extends ListRecords
{
    protected static string $resource = PunchDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
