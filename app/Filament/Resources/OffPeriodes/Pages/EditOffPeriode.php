<?php

namespace App\Filament\Resources\OffPeriodes\Pages;

use App\Filament\Resources\OffPeriodes\OffPeriodeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOffPeriode extends EditRecord
{
    protected static string $resource = OffPeriodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
