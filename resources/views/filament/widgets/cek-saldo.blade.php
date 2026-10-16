<x-filament-widgets::widget>
    <x-filament::section
        icon="heroicon-o-banknotes"
        icon-color="primary"
        heading="Cek Saldo"
        description="Cari nasabah berdasarkan nama atau nomor rekening"
    >
        <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
            <x-filament::input
                type="text"
                wire:model.live.debounce.500ms="search"
                placeholder="Cari nama atau no. rekening"
            />
        </x-filament::input.wrapper>

        @forelse ($this->accounts as $account)
            <div style="margin-top:1rem; padding:1.25rem; border-radius:0.75rem; color:white; background:#1e293b; border:1px solid #334155;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:.875rem; opacity:.85;">Saldo rekening</span>
                    <span style="font-size:0.75rem; font-weight:600; padding:0.125rem 0.6rem; border-radius:9999px; background-color:#16a34a; color:white;">Aktif</span>
                </div>

                <div style="font-size:2rem; font-weight:700; margin:.5rem 0 1rem;">
                    Rp {{ number_format($account->saldo, 0, ',', '.') }}
                </div>

                <div style="display:flex; justify-content:space-between; font-size:.875rem; opacity:.9;">
                    <span>{{ $account->customer->nama }}</span>
                    <span style="letter-spacing:.1em;">{{ $account->nomor_rekening }}</span>
                </div>
            </div>
        @empty
            @if (filled($search))
                <div style="margin-top:1.25rem; text-align:center; opacity:.7;">
                    Rekening tidak ditemukan.
                </div>
            @endif
        @endforelse
    </x-filament::section>
</x-filament-widgets::widget>