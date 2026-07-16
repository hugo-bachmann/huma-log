<?php

namespace App\Filament\Staff\Resources\Pages\Schemas;

use App\Enums\PageStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contenu')
                    ->schema([
                        TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        RichEditor::make('content')
                            ->label('Contenu')
                            ->columnSpanFull(),
                    ]),
                Section::make('SEO')
                    ->description('Métadonnées affichées dans les moteurs de recherche et le partage social.')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta title')
                            ->maxLength(255),
                        Textarea::make('meta_description')
                            ->label('Meta description')
                            ->maxLength(255),
                        FileUpload::make('og_image')
                            ->label('Image de partage (OG)')
                            ->image()
                            ->disk('public')
                            ->directory('pages/og'),
                    ]),
                Section::make('Publication')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Statut')
                            ->options(PageStatus::class)
                            ->default(PageStatus::Draft)
                            ->required(),
                        DateTimePicker::make('published_at')
                            ->label('Publiée le'),
                    ]),
            ]);
    }
}
