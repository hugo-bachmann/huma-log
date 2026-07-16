<?php

namespace App\Filament\Resources\UserJobs;

use App\Filament\Resources\UserJobs\Pages\ManageUserJobs;
use App\Filament\Resources\UserJobs\Schemas\UserJobForm;
use App\Filament\Resources\UserJobs\Tables\UserJobsTable;
use App\Models\UserJob;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserJobResource extends Resource
{
    protected static ?string $model = UserJob::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Postes';

    protected static ?string $modelLabel = 'poste';

    protected static ?string $pluralModelLabel = 'postes';

    public static function form(Schema $schema): Schema
    {
        return UserJobForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserJobsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUserJobs::route('/'),
        ];
    }
}
