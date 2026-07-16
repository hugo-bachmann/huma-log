<?php

namespace App\Filament\Resources\Histories\Pages;

use App\Enums\PunchMethod;
use App\Filament\Resources\Histories\HistoryResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateHistory extends CreateRecord
{
    protected static string $resource = HistoryResource::class;

    /**
     * Un pointage saisi ici est toujours manuel. company_id vient du tenant
     * courant et doit être posé avant que le modèle calcule son hash de chaîne.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = Filament::getTenant()->id;
        $data['method'] = PunchMethod::Manual;
        $data['created_by'] = auth()->id();

        return $data;
    }
}
