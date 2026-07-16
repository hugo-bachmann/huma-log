<?php

namespace App\Filament\Resources\OffPeriodes\Pages;

use App\Filament\Resources\OffPeriodes\OffPeriodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOffPeriodes extends ListRecords
{
    protected static string $resource = OffPeriodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nouvelle absence'),
        ];
    }
}
