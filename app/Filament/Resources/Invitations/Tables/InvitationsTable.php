<?php

namespace App\Filament\Resources\Invitations\Tables;

use App\Mail\TeamInvitation;
use App\Models\Invitation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class InvitationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('email')
                    ->label('Email')
                    ->weight('medium')
                    ->searchable(),
                TextColumn::make('role')
                    ->label('Rôle')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->state(fn (Invitation $record): string => match (true) {
                        $record->accepted_at !== null => 'Acceptée',
                        $record->isPending() => 'En attente',
                        default => 'Expirée',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Acceptée' => 'success',
                        'En attente' => 'warning',
                        default => 'danger',
                    }),
                TextColumn::make('expires_at')
                    ->label('Expire le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('resend')
                    ->label('Renvoyer')
                    ->icon(Heroicon::PaperAirplane)
                    ->visible(fn (Invitation $record): bool => $record->isPending())
                    ->action(fn (Invitation $record) => Mail::to($record->email)->send(new TeamInvitation($record)))
                    ->successNotificationTitle('Invitation renvoyée'),
                DeleteAction::make()
                    ->label('Révoquer'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
