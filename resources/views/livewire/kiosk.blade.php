<div class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-zinc-800 shadow-xl p-8">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $device->name }}</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Pointage</p>
        </div>

        @if ($feedback)
            <div class="mb-6 rounded-lg p-4 text-center text-lg font-medium {{ $feedback['ok'] ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200' }}">
                {{ $feedback['message'] }}
            </div>
        @endif

        <form wire:submit="punch" class="space-y-4">
            <div class="grid grid-cols-3 gap-2">
                @foreach (['nfc' => 'NFC', 'qr' => 'QR', 'pin' => 'PIN'] as $value => $label)
                    <button type="button" wire:click="$set('type', '{{ $value }}')"
                        class="rounded-lg border px-3 py-2 text-sm font-medium transition {{ $type === $value ? 'border-amber-500 bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-200' : 'border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-300' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <input type="text" wire:model="identifier" autofocus
                placeholder="Scannez le badge ou saisissez le code"
                class="w-full rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 px-4 py-3 text-center text-lg text-zinc-900 dark:text-white focus:border-amber-500 focus:outline-none" />
            @error('identifier')
                <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            <button type="submit"
                class="w-full rounded-lg bg-amber-500 px-4 py-3 text-lg font-semibold text-white hover:bg-amber-600 transition">
                Pointer
            </button>
        </form>
    </div>
</div>
