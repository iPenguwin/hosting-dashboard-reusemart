<x-filament::widget class="fi-account-widget">
    <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <!-- Avatar dan Informasi -->
        <div class="flex items-center gap-x-3">
            <div class="fi-avatar flex h-11 w-11 items-center justify-center overflow-hidden rounded-full border-2 border-gray-900 bg-black text-sm font-semibold text-black">
                {{ $this->getInitials() }}
            </div>
            <div>
                <h2 class="text-base font-semibold">
                    {{ filament()->getUserName(auth()->user()) }}<br>
                </h2>
                <p class="text-sm text-gray-300 leading-tight">
                    <span class="text-xs text-gray-400">{{ $this->getJabatan() }}</span>
                </p>
            </div>
        </div>

        <!-- Tombol Logout -->
        <form action="{{ filament()->getLogoutUrl() }}" method="post">
            @csrf
            <button type="submit" class="flex items-center gap-1 rounded-md border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                <x-heroicon-o-arrow-left-on-rectangle class="h-4 w-4" />
                {{ __('Sign out') }}
            </button>
        </form>
    </div>
</x-filament::widget>