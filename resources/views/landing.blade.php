@php
    $green = '#2FA875';
    $problems = [
        ['Excel partagé', 'Des tableurs que tout le monde modifie, personne ne maîtrise.'],
        ['Heures perdues en saisie', 'Recopier les pointages à la main chaque fin de mois.'],
        ['Litiges', 'Des désaccords sur les heures, faute de trace fiable.'],
        ['Heures supp. invisibles', 'Des dépassements que vous payez sans les voir venir.'],
    ];
    $withoutHumalog = ['Tableur partagé et fragile', 'Saisie manuelle chronophage', 'Aucune preuve en cas de litige', 'Heures supp. découvertes trop tard', 'Rapports paie bricolés', 'Zéro visibilité en temps réel'];
    $withHumalog = ['Pointage horodaté infalsifiable', 'Calcul automatique des heures', 'Historique complet et signé', 'Alertes heures supp. en direct', 'Export paie en un clic', 'Tableau de bord temps réel'];
    $features = [
        ['Pointage en un geste', 'Badge, QR ou code PIN sur la borne — l\'entrée est enregistrée en une seconde.'],
        ['Pauses suivies', 'Chaque pause est comptée, les règles légales appliquées automatiquement.'],
        ['Rapports paie', 'Un récapitulatif mensuel prêt pour la paie, exportable en PDF.'],
        ['Heures supp. maîtrisées', 'Les dépassements sont calculés et signalés au fil de l\'eau.'],
        ['Alertes intelligentes', 'Oubli de pointage, anomalie, retard : vous êtes prévenu à temps.'],
        ['Conformité garantie', 'Un historique horodaté et infalsifiable, opposable en cas de contrôle.'],
    ];
    $reviews = [
        ['« On a récupéré une demi-journée par mois sur la paie. Simple, fiable, adopté par toute l\'équipe. »', 'Sophie Marceau', 'Gérante · Boulangerie Marceau'],
        ['« Fini les litiges sur les heures. Chaque pointage est tracé, tout le monde joue le jeu. »', 'Bruno Lefèvre', 'Dirigeant · Atelier Mécanique'],
        ['« Installation en cinq minutes sur une tablette. Le support est réactif et humain. »', 'Émilie Goncalves', 'Responsable · Coiffure Studio'],
    ];
    $plans = [
        ['Découverte', 'Pour les petites équipes', '4', '3', ['Jusqu\'à 10 collaborateurs', 'Pointage borne & mobile', 'Rapports mensuels', 'Support email'], false],
        ['Atelier', 'Le plus choisi', '7', '5', ['Collaborateurs illimités', 'Plannings & absences', 'Alertes intelligentes', 'Export paie & API', 'Support prioritaire'], true],
        ['Sur mesure', 'Pour les groupes', null, null, ['Multi-établissements', 'SSO & rôles avancés', 'Accompagnement dédié', 'SLA garanti'], false],
    ];
    $faqs = [
        ['Combien de temps pour installer Huma·log ?', 'Cinq minutes : créez votre espace, ajoutez une tablette comme borne, invitez vos collaborateurs. Aucun matériel spécifique requis.'],
        ['Les pointages sont-ils infalsifiables ?', 'Oui. Chaque pointage est horodaté et chaîné cryptographiquement au précédent : toute modification casse la chaîne et devient détectable.'],
        ['Puis-je corriger un oubli de pointage ?', 'Un manager peut saisir un pointage manuel. La correction est tracée dans l\'audit, l\'original n\'est jamais écrasé.'],
        ['Comment sont calculées les heures supplémentaires ?', 'Automatiquement, au-delà du seuil hebdomadaire que vous définissez. Elles apparaissent en temps réel sur le tableau de bord.'],
        ['Mes données sont-elles hébergées en France ?', 'Oui, chez un hébergeur français, chiffrées au repos et en transit, avec des sauvegardes régulières.'],
        ['Puis-je exporter pour ma paie ?', 'Un récapitulatif PDF par employé et par période est généré en un clic, prêt à transmettre à votre gestionnaire de paie.'],
        ['Y a-t-il un engagement ?', 'Non. Sans engagement, sans carte bancaire pour l\'essai, résiliable à tout moment.'],
    ];
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Huma·log — La pointeuse en ligne pour les équipes terrain</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=geist:400,500,600,700">
    @vite(['resources/css/app.css'])
    <style>
        [x-cloak]{display:none!important}
        body{font-family:'Geist',ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,sans-serif}
    </style>
    @livewireStyles
</head>
<body
    class="bg-white text-zinc-900 antialiased"
    x-data="{ scrolled: false, contactOpen: {{ ($errors->any() || session('lead_sent')) ? 'true' : 'false' }} }"
    @scroll.window="scrolled = window.scrollY > 8"
>
    {{-- NAV --}}
    <nav
        class="fixed inset-x-0 top-0 z-50 transition-all duration-500"
        :class="scrolled ? 'bg-white/95 backdrop-blur border-b border-zinc-200' : 'bg-transparent'"
    >
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <a href="{{ route('home') }}" class="text-lg font-semibold tracking-tight">Huma<span style="color: {{ $green }}">·</span>log</a>
            <div class="hidden items-center gap-8 text-sm text-zinc-600 md:flex">
                <a href="#fonctionnalites" class="hover:text-zinc-900">Fonctionnalités</a>
                <a href="#avantages" class="hover:text-zinc-900">Avantages</a>
                <a href="#avis" class="hover:text-zinc-900">Avis</a>
                <a href="#tarifs" class="hover:text-zinc-900">Tarifs</a>
                <a href="#faq" class="hover:text-zinc-900">FAQ</a>
            </div>
            <div class="flex items-center gap-3">
                <a href="/app/login" class="text-sm text-zinc-600 hover:text-zinc-900">Connexion</a>
                <button @click="contactOpen = true" class="rounded-full px-4 py-2 text-sm font-medium text-white transition hover:opacity-90" style="background: {{ $green }}">Essai gratuit</button>
            </div>
        </div>
    </nav>

    {{-- HERO --}}
    <header class="px-6 pt-40 pb-20 text-center">
        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm font-medium" style="background: #E8F5EE; color: #155F40">
            Pointeuse en ligne · pour les équipes terrain
        </span>
        <h1 class="mx-auto mt-6 max-w-4xl text-5xl font-medium leading-[1.05] tracking-tight sm:text-6xl">
            Le temps de travail, <span style="color: {{ $green }}">compté juste</span>. Sans effort, sans litige.
        </h1>
        <p class="mx-auto mt-6 max-w-xl text-lg text-zinc-600">
            Huma·log enregistre chaque entrée et sortie de façon infalsifiable, calcule les heures et prépare votre paie. Automatiquement.
        </p>
        <div class="mt-8 flex items-center justify-center gap-3">
            <button @click="contactOpen = true" class="rounded-full px-6 py-3 text-sm font-medium text-white transition hover:opacity-90" style="background: {{ $green }}">Commencer l'essai gratuit</button>
            <a href="#fonctionnalites" class="rounded-full border border-zinc-300 px-6 py-3 text-sm font-medium text-zinc-900 transition hover:bg-zinc-50">Voir une démo (2 mn)</a>
        </div>
        <p class="mt-6 text-sm text-zinc-400">
            14 jours · Aucune carte bancaire · Installation en 5 minutes
        </p>

        {{-- App mockup --}}
        <div class="mx-auto mt-16 max-w-4xl overflow-hidden rounded-2xl border border-zinc-200 text-left shadow-[0_24px_48px_-12px_rgba(9,9,11,0.12)]">
            <div class="flex items-center gap-2 border-b border-zinc-200 bg-zinc-50 px-4 py-3">
                <span class="h-3 w-3 rounded-full bg-[#FF5F57]"></span>
                <span class="h-3 w-3 rounded-full bg-[#FEBC2E]"></span>
                <span class="h-3 w-3 rounded-full bg-[#28C840]"></span>
                <span class="ml-3 text-xs text-zinc-400">app.huma-log.fr/tableau-de-bord</span>
            </div>
            <div class="grid sm:grid-cols-[200px_1fr]">
                <aside class="hidden flex-col gap-1 border-r border-zinc-200 bg-zinc-50 p-4 text-sm sm:flex">
                    <span class="rounded-lg border border-zinc-200 bg-white px-3 py-2 font-medium">Tableau de bord</span>
                    @foreach (['Employés', 'Historique', 'Plannings', 'Absences'] as $nav)
                        <span class="px-3 py-2 text-zinc-500">{{ $nav }}</span>
                    @endforeach
                </aside>
                <div class="p-6">
                    <div class="rounded-xl border border-[#C6E8D6] p-5" style="background: linear-gradient(180deg, #F2FAF6, #FFFFFF)">
                        <div class="flex items-center gap-2 text-sm text-zinc-500">
                            <span class="h-2 w-2 rounded-full" style="background: {{ $green }}"></span> Pointage en cours · Marc Dubois
                        </div>
                        <div class="mt-2 text-4xl font-medium tabular-nums" x-data="{ now: '00:00:00' }" x-init="setInterval(() => now = new Date().toLocaleTimeString('fr-FR'), 1000)">
                            <span x-text="now">08:14:32</span>
                        </div>
                    </div>
                    <div class="mt-4 space-y-2">
                        @foreach ([['Léa Bernard', 'Présent', $green], ['Karim Yacef', 'Pause', '#E0A11F'], ['Anne-Sophie L.', 'Absent', '#9F9F9F']] as [$name, $status, $dot])
                            <div class="flex items-center justify-between rounded-lg border border-zinc-100 px-4 py-2.5 text-sm">
                                <span class="font-medium">{{ $name }}</span>
                                <span class="flex items-center gap-2 text-zinc-500"><span class="h-2 w-2 rounded-full" style="background: {{ $dot }}"></span>{{ $status }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- LOGO BAR --}}
    <section class="px-6 py-8">
        <p class="text-center text-xs font-medium uppercase tracking-wide text-zinc-400">Plus de 800 PME et commerces utilisent Huma·log au quotidien</p>
        <div class="mx-auto mt-6 flex max-w-4xl flex-wrap items-center justify-center gap-x-10 gap-y-4 text-zinc-400">
            @foreach (['Marceau', 'Renault Atelier', 'Studio Lyon', 'Café des Arts', 'Pressing Express', 'Da Marco'] as $brand)
                <span class="font-medium">{{ $brand }}</span>
            @endforeach
        </div>
    </section>

    {{-- PROBLÈME --}}
    <section class="px-6 py-24">
        <div class="mx-auto max-w-6xl">
            <span class="rounded-full px-3 py-1 text-sm font-medium" style="background: #FBECEC; color: #A83030">Le problème</span>
            <h2 class="mt-4 max-w-2xl text-4xl font-medium tracking-tight">Compter le temps de travail, ça vous coûte du temps.</h2>
            <div class="mt-10 grid gap-4 sm:grid-cols-2">
                @foreach ($problems as [$title, $desc])
                    <div class="rounded-xl border border-zinc-200 p-7 transition hover:border-zinc-900">
                        <h3 class="font-medium">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-zinc-600">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- AGITATION / COMPARATIF --}}
    <section id="avantages" class="bg-zinc-50 px-6 py-24">
        <div class="mx-auto max-w-6xl">
            <span class="rounded-full bg-zinc-200 px-3 py-1 text-sm font-medium text-zinc-600">Avant · Après</span>
            <h2 class="mt-4 max-w-2xl text-4xl font-medium tracking-tight">La différence se voit dès la première semaine.</h2>
            <div class="mt-10 grid gap-5 md:grid-cols-2">
                <div class="rounded-2xl border border-zinc-200 bg-white p-8">
                    <p class="font-medium text-zinc-500">Sans Huma·log</p>
                    <ul class="mt-4 space-y-3 text-sm text-zinc-600">
                        @foreach ($withoutHumalog as $item)
                            <li class="flex gap-3"><span class="text-[#A83030]">✕</span>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="rounded-2xl border-2 bg-white p-8" style="border-color: {{ $green }}; box-shadow: 0 0 0 4px rgba(47,168,117,0.10)">
                    <p class="font-medium" style="color: {{ $green }}">Avec Huma·log</p>
                    <ul class="mt-4 space-y-3 text-sm text-zinc-700">
                        @foreach ($withHumalog as $item)
                            <li class="flex gap-3"><span style="color: {{ $green }}">✓</span>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="mt-8 grid grid-cols-3 gap-4 rounded-2xl bg-white p-8 text-center">
                @foreach ([['−87 %', 'de temps de saisie'], ['+12 h', 'récupérées par mois'], ['0', 'litige sur les heures']] as [$stat, $label])
                    <div>
                        <div class="text-4xl font-medium" style="color: {{ $green }}">{{ $stat }}</div>
                        <div class="mt-1 text-sm text-zinc-500">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- SOLUTION --}}
    <section id="fonctionnalites" class="px-6 py-24">
        <div class="mx-auto max-w-6xl">
            <span class="rounded-full px-3 py-1 text-sm font-medium" style="background: #E8F5EE; color: #155F40">La solution</span>
            <h2 class="mt-4 max-w-2xl text-4xl font-medium tracking-tight">Une pointeuse qui fait son travail. Pas plus, pas moins.</h2>
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($features as [$title, $desc])
                    <div class="rounded-xl border border-zinc-200 p-7 transition hover:border-[#2FA875]">
                        <h3 class="font-medium">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-zinc-600">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- AVIS --}}
    <section id="avis" class="px-6 py-24">
        <div class="mx-auto max-w-6xl">
            <span class="rounded-full bg-zinc-200 px-3 py-1 text-sm font-medium text-zinc-600">Avis clients</span>
            <h2 class="mt-4 text-4xl font-medium tracking-tight">Ce que nos clients en disent.</h2>
            <p class="mt-2 text-zinc-600">Note moyenne 4,9 / 5 sur 312 avis vérifiés</p>
            <div class="mt-10 grid gap-5 lg:grid-cols-3">
                @foreach ($reviews as [$quote, $name, $role])
                    <div class="rounded-2xl border border-zinc-200 p-8">
                        <div style="color: {{ $green }}">★★★★★</div>
                        <p class="mt-4 text-zinc-900">{{ $quote }}</p>
                        <p class="mt-6 text-sm font-medium">{{ $name }}</p>
                        <p class="text-sm text-zinc-500">{{ $role }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- TARIFS --}}
    <section id="tarifs" class="bg-zinc-50 px-6 py-24" x-data="{ annual: false }">
        <div class="mx-auto max-w-6xl text-center">
            <span class="rounded-full bg-zinc-200 px-3 py-1 text-sm font-medium text-zinc-600">Tarifs</span>
            <h2 class="mt-4 text-4xl font-medium tracking-tight">Un prix par collaborateur, par mois. Sans surprise.</h2>
            <div class="mt-8 inline-flex items-center gap-1 rounded-full border border-zinc-200 bg-white p-1 text-sm">
                <button @click="annual = false" :class="!annual && 'bg-zinc-900 text-white'" class="rounded-full px-4 py-1.5 font-medium">Mensuel</button>
                <button @click="annual = true" :class="annual && 'bg-zinc-900 text-white'" class="rounded-full px-4 py-1.5 font-medium">Annuel <span style="color: {{ $green }}" :class="annual && '!text-white'">−30 %</span></button>
            </div>
            <div class="mt-10 grid gap-5 md:grid-cols-3">
                @foreach ($plans as [$name, $tagline, $monthly, $annualPrice, $items, $featured])
                    <div class="rounded-2xl border bg-white p-8 text-left {{ $featured ? 'border-2' : 'border-zinc-200' }}" @style(["border-color: $green" => $featured, "box-shadow: 0 0 0 4px rgba(47,168,117,0.10)" => $featured])>
                        @if ($featured)<span class="rounded-full px-3 py-1 text-xs font-medium text-white" style="background: {{ $green }}">Le plus choisi</span>@endif
                        <h3 class="mt-3 text-lg font-medium">{{ $name }}</h3>
                        <p class="text-sm text-zinc-500">{{ $tagline }}</p>
                        <div class="mt-4">
                            @if ($monthly)
                                <span class="text-5xl font-medium"><span x-text="annual ? '{{ $annualPrice }}' : '{{ $monthly }}'">{{ $monthly }}</span> €</span>
                                <span class="text-sm text-zinc-500">/ collaborateur / mois</span>
                            @else
                                <span class="text-4xl font-medium">Sur devis</span>
                            @endif
                        </div>
                        <ul class="mt-6 space-y-2 text-sm text-zinc-600">
                            @foreach ($items as $item)
                                <li class="flex gap-2"><span style="color: {{ $green }}">✓</span>{{ $item }}</li>
                            @endforeach
                        </ul>
                        <button @click="contactOpen = true" class="mt-6 w-full rounded-full py-2.5 text-sm font-medium {{ $featured ? 'text-white' : 'border border-zinc-300 text-zinc-900 hover:bg-zinc-50' }}" @style(["background: $green" => $featured])>Commencer</button>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- DARK CTA --}}
    <section class="px-6 py-8">
        <div class="mx-auto max-w-6xl rounded-3xl px-10 py-20 text-center" style="background: linear-gradient(90deg, #09090B 30%, #4D4C55 100%)">
            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-sm" style="background: rgba(47,168,117,0.18); color: #A8E5C5">14 jours offerts · sans carte bancaire</span>
            <h2 class="mx-auto mt-6 max-w-2xl text-4xl font-medium tracking-tight text-white sm:text-5xl">Reprenez la main sur le temps de travail.</h2>
            <div class="mt-8 flex items-center justify-center gap-3">
                <button @click="contactOpen = true" class="rounded-full px-6 py-3 text-sm font-medium text-white" style="background: {{ $green }}">Démarrer gratuitement</button>
                <button @click="contactOpen = true" class="rounded-full bg-white px-6 py-3 text-sm font-medium text-zinc-900">Réserver une démo</button>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="px-6 py-24" x-data="{ open: 0 }">
        <div class="mx-auto max-w-3xl">
            <span class="rounded-full bg-zinc-200 px-3 py-1 text-sm font-medium text-zinc-600">FAQ</span>
            <h2 class="mt-4 text-4xl font-medium tracking-tight">Des questions ? On y répond.</h2>
            <div class="mt-8 divide-y divide-zinc-200 border-t border-zinc-200">
                @foreach ($faqs as $i => [$q, $a])
                    <div>
                        <button @click="open = open === {{ $i }} ? -1 : {{ $i }}" class="flex w-full items-center justify-between py-5 text-left font-medium">
                            {{ $q }}
                            <span class="ml-4 grid h-7 w-7 shrink-0 place-items-center rounded-full border border-zinc-300 transition" :class="open === {{ $i }} && 'rotate-45'">+</span>
                        </button>
                        <div x-show="open === {{ $i }}" x-collapse.duration.300ms x-cloak>
                            <p class="pb-5 text-zinc-600">{{ $a }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-zinc-950 px-6 py-16 text-zinc-400">
        <div class="mx-auto flex max-w-6xl flex-col justify-between gap-8 sm:flex-row">
            <div>
                <p class="text-lg font-semibold text-white">Huma<span style="color: {{ $green }}">·</span>log</p>
                <p class="mt-2 max-w-xs text-sm">La pointeuse en ligne pour les PME et commerces. Le temps de travail, compté juste.</p>
            </div>
            <div class="flex gap-16 text-sm">
                <div>
                    <p class="mb-3 text-xs uppercase tracking-wide text-zinc-500">Produit</p>
                    <ul class="space-y-2">
                        <li><a href="#fonctionnalites" class="hover:text-white">Fonctionnalités</a></li>
                        <li><a href="#tarifs" class="hover:text-white">Tarifs</a></li>
                        <li><a href="/app/login" class="hover:text-white">Connexion</a></li>
                    </ul>
                </div>
                <div>
                    <p class="mb-3 text-xs uppercase tracking-wide text-zinc-500">Entreprise</p>
                    <ul class="space-y-2">
                        <li><a href="#avis" class="hover:text-white">Avis</a></li>
                        <li><a href="#faq" class="hover:text-white">FAQ</a></li>
                        <li><button @click="contactOpen = true" class="hover:text-white">Contact</button></li>
                    </ul>
                </div>
            </div>
        </div>
        <p class="mx-auto mt-12 max-w-6xl text-xs text-zinc-600">© {{ date('Y') }} Huma·log · Mentions légales</p>
    </footer>

    {{-- CONTACT MODAL --}}
    <div x-show="contactOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="display: none;">
        <div class="absolute inset-0 bg-zinc-950/50" @click="contactOpen = false"></div>
        <div class="relative w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
            <button @click="contactOpen = false" class="absolute right-5 top-5 text-zinc-400 hover:text-zinc-900">✕</button>
            @if (session('lead_sent'))
                <div class="py-6 text-center">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl text-2xl" style="background: #E8F5EE; color: {{ $green }}">✓</div>
                    <h3 class="mt-4 text-xl font-medium">Message envoyé.</h3>
                    <p class="mt-2 text-sm text-zinc-600">Merci ! Notre équipe vous recontacte sous 24 h ouvrées.</p>
                    <button @click="contactOpen = false" class="mt-6 rounded-full px-5 py-2 text-sm font-medium text-white" style="background: {{ $green }}">Fermer</button>
                </div>
            @else
                <h3 class="text-xl font-medium">Démarrer avec Huma·log</h3>
                <p class="mt-1 text-sm text-zinc-600">Laissez-nous vos coordonnées, on s'occupe du reste.</p>
                <form method="POST" action="{{ route('contact.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <input name="name" value="{{ old('name') }}" placeholder="Votre nom" class="w-full rounded-lg border border-zinc-300 px-3 py-2.5 text-sm outline-none focus:border-zinc-900" required>
                        @error('name')<p class="mt-1 text-xs text-[#A83030]">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <input name="email" type="email" value="{{ old('email') }}" placeholder="Email professionnel" class="w-full rounded-lg border border-zinc-300 px-3 py-2.5 text-sm outline-none focus:border-zinc-900" required>
                        @error('email')<p class="mt-1 text-xs text-[#A83030]">{{ $message }}</p>@enderror
                    </div>
                    <input name="company_name" value="{{ old('company_name') }}" placeholder="Entreprise" class="w-full rounded-lg border border-zinc-300 px-3 py-2.5 text-sm outline-none focus:border-zinc-900">
                    <select name="size" class="w-full rounded-lg border border-zinc-300 px-3 py-2.5 text-sm outline-none focus:border-zinc-900">
                        <option value="">Effectif (optionnel)</option>
                        @foreach (['1-10', '11-30', '31-50', '50+'] as $s)
                            <option value="{{ $s }}" @selected(old('size') === $s)>{{ $s }} collaborateurs</option>
                        @endforeach
                    </select>
                    <textarea name="message" rows="3" placeholder="Votre message (optionnel)" class="w-full rounded-lg border border-zinc-300 px-3 py-2.5 text-sm outline-none focus:border-zinc-900">{{ old('message') }}</textarea>
                    <button type="submit" class="w-full rounded-lg py-2.5 text-sm font-medium text-white" style="background: {{ $green }}">Envoyer ma demande</button>
                </form>
            @endif
        </div>
    </div>

    @livewireScripts
</body>
</html>
