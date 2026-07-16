<?php

namespace App\Filament\Staff\Resources\Companies\Pages;

use App\Filament\Staff\Resources\Companies\CompanyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanies extends ListRecords
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nouvelle entreprise'),
        ];
    }
}
