<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class AcceptInvitationController extends Controller
{
    public function __invoke(Invitation $invitation): RedirectResponse
    {
        if (! $invitation->isPending()) {
            return redirect()->route('login')
                ->with('status', 'Cette invitation n\'est plus valide.');
        }

        $user = auth()->user();

        // Invité non connecté : on le renvoie vers l'inscription (nouveau) ou la
        // connexion (compte existant), puis on revient ici via url.intended.
        if ($user === null) {
            session(['url.intended' => route('invitation.accept', $invitation->token)]);

            $route = User::where('email', $invitation->email)->exists() ? 'login' : 'register';

            return redirect()->route($route)
                ->with('status', 'Connectez-vous avec '.$invitation->email.' pour accepter l\'invitation.');
        }

        // Connecté mais avec un autre email que celui invité.
        abort_unless($user->email === $invitation->email, 403, 'Cette invitation ne correspond pas à votre compte.');

        $invitation->accept($user);

        return redirect('/app')
            ->with('status', 'Bienvenue dans '.$invitation->company->name.' !');
    }
}
