<x-filament-widgets::widget>
    <x-filament::section>
        <div class="p-2">

            {{-- Input Select Pencarian Nasabah --}}
            <div class="w-full mb-3">
                {{ $this->form }}
            </div>

            {{-- Kotakan Card Hasil Data Nasabah (Tanda Beureum) --}}
            @if($account)
                <div class="w-full p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm text-left">
                    <div class="flex items-center justify-between pb-2 mb-3 border-b border-gray-100 dark:border-gray-800">
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Detail Rekening</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            Aktif
                        </span>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Nomor Rekening</span>
                            <span class="text-sm font-mono font-bold text-gray-800 dark:text-gray-200">{{ $account->nomor_rekening }}</span>
                        </div>

                        <div class="flex justify-between items-center pt-1">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Total Saldo</span>
                            <span class="text-lg font-extrabold text-primary-600 dark:text-primary-400">
                                Rp {{ number_format($account->saldo ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </x-filament::section>
</x-filament-widgets::widget>