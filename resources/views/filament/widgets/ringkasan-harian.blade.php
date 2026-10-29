<x-filament-widgets::widget>
    <div style="padding: .5rem .25rem;">
        <div style="display: flex; align-items: center; gap: .875rem;">
            <img src="{{ asset('images/logo-smk.png') }}"
                 alt="Logo SMK 1 Parahyangan"
                 style="height: 3rem; width: auto; object-fit: contain;">
            <div>
                <div style="font-size: .875rem; font-weight: 700;">
                    Selamat bekerja, Admin
                </div>
                <div style="font-size: .8125rem; opacity: .65;">
                    {{ now()->locale('id')->translatedFormat('l, d F Y') }}
                </div>
                <div style="font-size: 1.25rem; font-weight: 500; margin-top: .125rem;">
                    Teaching Factory Parahyangan
                </div>
            </div>
        </div>

        {{-- Grid 4 Statistik --}}
        <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1.5rem;
                    margin-top: 1.5rem; padding-bottom: 1.5rem;
                    border-bottom: 1px solid rgba(128, 128, 128, .25);">

            {{-- 1. Nasabah --}}
            <div style="display: flex; align-items: center; gap: .75rem;">
                <div style="width: 2.5rem; height: 2.5rem; border-radius: .5rem; display: flex;
                            align-items: center; justify-content: center;
                            background: rgba(239, 68, 68, .12); color: #ef4444;">
                    <x-filament::icon icon="heroicon-o-user-group" style="width: 1.25rem; height: 1.25rem;" />
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 500; line-height: 1.1;">
                        {{ number_format($this->ringkasan['nasabah'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div style="font-size: .8125rem; opacity: .65; margin-top: .25rem;">nasabah</div>
                </div>
            </div>

            {{-- 2. Rekening Aktif --}}
            <div style="display: flex; align-items: center; gap: .75rem;">
                <div style="width: 2.5rem; height: 2.5rem; border-radius: .5rem; display: flex;
                            align-items: center; justify-content: center;
                            background: rgba(239, 68, 68, .12); color: #ef4444;">
                    <x-filament::icon icon="heroicon-o-credit-card" style="width: 1.25rem; height: 1.25rem;" />
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 500; line-height: 1.1;">
                        {{ number_format($this->ringkasan['rekening_aktif'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div style="font-size: .8125rem; opacity: .65; margin-top: .25rem;">rekening aktif</div>
                </div>
            </div>

            {{-- 3. Transaksi Hari Ini --}}
            <div style="display: flex; align-items: center; gap: .75rem;">
                <div style="width: 2.5rem; height: 2.5rem; border-radius: .5rem; display: flex;
                            align-items: center; justify-content: center;
                            background: rgba(239, 68, 68, .12); color: #ef4444;">
                    <x-filament::icon icon="heroicon-o-arrows-right-left" style="width: 1.25rem; height: 1.25rem;" />
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 500; line-height: 1.1;">
                        {{ number_format($this->ringkasan['transaksi_hari_ini'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div style="font-size: .8125rem; opacity: .65; margin-top: .25rem;">transaksi hari ini</div>
                </div>
            </div>

            {{-- 4. Uang Terkumpul Hari Ini --}}
            <div style="display: flex; align-items: center; gap: .75rem;">
                <div style="width: 2.5rem; height: 2.5rem; border-radius: .5rem; display: flex;
                            align-items: center; justify-content: center;
                            background: rgba(34, 197, 94, .12); color: #22c55e;">
                    <x-filament::icon icon="heroicon-o-banknotes" style="width: 1.25rem; height: 1.25rem;" />
                </div>
                <div>
                    <div style="font-size: 1.5rem; font-weight: 500; line-height: 1.1;">
                        Rp {{ number_format($this->ringkasan['uang_terkumpul'] ?? 0, 0, ',', '.') }}
                    </div>
                    <div style="font-size: .8125rem; opacity: .65; margin-top: .25rem;">uang terkumpul hari ini</div>
                </div>
            </div>

        </div>
    </div>
</x-filament-widgets::widget>