@php($googleConfigured = filled(config('services.google.client_id')))
@if ($googleConfigured)
    <div class="fi-sc-has-gap flex flex-col gap-y-4">
        <div class="flex items-center gap-x-3 text-xs text-gray-400">
            <span class="h-px flex-1 bg-gray-200 dark:bg-white/10"></span>
            ou
            <span class="h-px flex-1 bg-gray-200 dark:bg-white/10"></span>
        </div>
        <a href="{{ route('auth.google.redirect') }}"
           class="fi-btn fi-btn-size-md relative grid-flow-col items-center justify-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-950 shadow-sm outline-none transition hover:bg-gray-50 dark:border-white/10 dark:bg-white/5 dark:text-white dark:hover:bg-white/10">
            <span class="grid justify-items-center text-center">Continuer avec Google</span>
        </a>
    </div>
@endif
