<?php

namespace App\Filament\Staff\Resources\Users\Pages;

use App\Filament\Staff\Resources\Users\UserResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * `is_staff` est volontairement hors du $fillable (flag d'accès super-admin).
     * On le pose via forceFill dans ce contexte staff de confiance. Le cast
     * `hashed` du password s'applique toujours.
     *
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        $user = new User;
        $user->forceFill($data)->save();

        return $user;
    }
}
