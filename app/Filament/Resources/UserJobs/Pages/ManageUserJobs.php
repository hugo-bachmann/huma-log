<?php

namespace App\Filament\Resources\UserJobs\Pages;

use App\Filament\Resources\UserJobs\UserJobResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageUserJobs extends ManageRecords
{
    protected static string $resource = UserJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Ajouter un poste'),
        ];
    }
}
