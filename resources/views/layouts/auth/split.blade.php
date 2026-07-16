<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
            <div class="relative hidden h-full flex-col p-10 text-white lg:flex">
                <div class="absolute inset-0" style="background: linear-gradient(120deg, #09090B 30%, #1F8A5B 140%)"></div>
                <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-semibold tracking-tight" wire:navigate>
                    Huma<span style="color: #2FA875">·</span>log
                </a>

                <div class="relative z-20 mt-auto">
                    <blockquote class="space-y-4">
                        <p class="text-2xl font-medium leading-snug tracking-tight">
                            « Une signature horaire pour chaque collaborateur. Le temps de travail, compté juste. »
                        </p>
                        <footer class="text-sm text-white/70">Mathis R. — DRH, groupe de 40 personnes</footer>
                    </blockquote>

                    <div class="mt-10 grid grid-cols-3 gap-4 border-t border-white/10 pt-6">
                        @foreach ([['800+', 'entreprises'], ['−87 %', 'de saisie'], ['14 j', 'offerts']] as [$stat, $label])
                            <div>
                                <div class="text-xl font-medium tabular-nums" style="color: #A8E5C5">{{ $stat }}</div>
                                <div class="text-xs text-white/60">{{ $label }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="w-full lg:p-8">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                    <a href="{{ route('home') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate>
                        <span class="flex h-9 w-9 items-center justify-center rounded-md">
                            <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                        </span>

                        <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                    </a>
                    {{ $slot }}
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
