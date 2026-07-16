<?php

namespace App\Models;

use App\Enums\CompanyRole;
use App\Mail\TeamInvitation;
use Database\Factories\InvitationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

#[Fillable(['company_id', 'email', 'role', 'token', 'expires_at', 'accepted_at'])]
class Invitation extends Model
{
    /** @use HasFactory<InvitationFactory> */
    use HasFactory;

    /**
     * Durée de validité d'une invitation.
     */
    public const VALID_DAYS = 7;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => CompanyRole::class,
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invitation $invitation): void {
            $invitation->token ??= Str::random(40);
            $invitation->expires_at ??= now()->addDays(self::VALID_DAYS);
        });

        // Un seul point d'envoi : toutes les voies de création (wizard, resource) notifient.
        static::created(function (Invitation $invitation): void {
            Mail::to($invitation->email)->send(new TeamInvitation($invitation));
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isPending(): bool
    {
        return $this->accepted_at === null && $this->expires_at->isFuture();
    }

    /**
     * Rattache l'utilisateur à l'entreprise avec le rôle de l'invitation.
     */
    public function accept(User $user): void
    {
        $this->company->users()->syncWithoutDetaching([
            $user->id => ['role' => $this->role->value],
        ]);

        $this->forceFill(['accepted_at' => now()])->save();
    }
}
