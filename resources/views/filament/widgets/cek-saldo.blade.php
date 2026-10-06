<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">

            <h2 class="text-lg font-bold">
                Cek Saldo
            </h2>

            <x-filament::input.wrapper>
                <x-filament::input
                    type="text"
                    wire:model.live="search"
                    placeholder="Cari nama atau nomor rekening..."
                />
            </x-filament::input.wrapper>

            @if ($search)
                @forelse ($this->accounts as $account)

                    <div class="rounded-lg border p-4">
                        <div class="font-bold text-lg">
                            {{ $account->customer->nama }}
                        </div>

                        <div class="text-sm text-gray-500">
                            No. Rekening:
                            {{ $account->nomor_rekening }}
                        </div>

                        <div class="mt-2 text-xl font-bold">
                            Rp {{ number_format($account->saldo, 0, ',', '.') }}
                        </div>
                    </div>

                @empty

                    <div class="text-sm text-gray-500">
                        Data rekening tidak ditemukan.
                    </div>

                @endforelse
            @endif

        </div>
    </x-filament::section>
</x-filament-widgets::widget>