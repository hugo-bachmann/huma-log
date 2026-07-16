<?php

namespace App\Filament\Resources\Employees\Pages;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Services\EmployeeCsvImporter;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Importer un CSV')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->schema([
                    FileUpload::make('file')
                        ->label('Fichier CSV')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/csv'])
                        ->helperText('Colonnes : first_name, last_name, email, phone, job_title.')
                        ->storeFiles(false)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $result = (new EmployeeCsvImporter)->import(
                        Filament::getTenant(),
                        $data['file']->getRealPath(),
                    );

                    Notification::make()
                        ->title("{$result['created']} employé(s) importé(s)")
                        ->body(filled($result['errors']) ? implode("\n", array_slice($result['errors'], 0, 5)) : null)
                        ->color(filled($result['errors']) ? 'warning' : 'success')
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
