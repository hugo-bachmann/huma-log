<x-mail::message>
# Invitation à rejoindre {{ $company }}

Vous avez été invité·e à rejoindre l'espace **{{ $company }}** sur Huma·log en tant que **{{ $role }}**.

<x-mail::button :url="$url">
Accepter l'invitation
</x-mail::button>

Ce lien est valable {{ \App\Models\Invitation::VALID_DAYS }} jours. Si vous n'attendiez pas cette invitation, ignorez simplement cet email.

Merci,<br>
L'équipe Huma·log
</x-mail::message>
