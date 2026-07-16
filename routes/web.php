<?php

use App\Http\Controllers\AcceptInvitationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\StoreLeadController;
use App\Livewire\Kiosk;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('home');
Route::post('/contact', StoreLeadController::class)->name('contact.store');

// Kiosk de pointage : la borne s'authentifie par son token (dans l'URL), pas de login.
Route::get('/kiosk/{device:token}', Kiosk::class)->name('kiosk');

// Acceptation d'une invitation d'équipe (le contrôleur gère connecté / invité / expiré).
Route::get('/invitation/{invitation:token}', AcceptInvitationController::class)->name('invitation.accept');

// Connexion via Google (OAuth Socialite).
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
