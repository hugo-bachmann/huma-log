<?php

namespace App\Filament\Resources\PunchDevices\Pages;

use App\Filament\Resources\PunchDevices\PunchDeviceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPunchDevice extends EditRecord
{
    protected static string $resource = PunchDeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
