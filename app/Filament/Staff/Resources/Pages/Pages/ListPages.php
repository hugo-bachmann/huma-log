<?php

namespace App\Filament\Staff\Resources\Pages\Pages;

use App\Filament\Staff\Resources\Pages\PageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPages extends ListRecords
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nouvelle page'),
        ];
    }
}
