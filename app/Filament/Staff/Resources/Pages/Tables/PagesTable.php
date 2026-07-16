<?php

namespace App\Filament\Staff\Resources\Pages\Tables;

use App\Enums\PageStatus;
use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->weight('medium')
                    ->description(fn (Page $record) => '/'.$record->slug)
                    ->searchable(['title', 'slug']),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge(),
                TextColumn::make('published_at')
                    ->label('Publiée le')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Modifiée')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(PageStatus::class),
            ])
            ->recordActions([
                Action::make('publish')
                    ->label('Publier')
                    ->icon(Heroicon::PaperAirplane)
                    ->color('success')
                    ->visible(fn (Page $record): bool => $record->status === PageStatus::Draft)
                    ->requiresConfirmation()
                    ->action(fn (Page $record) => $record->update([
                        'status' => PageStatus::Published,
                        'published_at' => $record->published_at ?? now(),
                    ])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
