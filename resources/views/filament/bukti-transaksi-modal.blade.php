<div style="font-size: 14px;">
    <div style="text-align: center; margin-bottom: 12px;">
        <div style="font-weight: 600; font-size: 16px;">Tabungan Sekolah</div>
        <div style="opacity: .6; font-size: 13px;">Bukti transaksi</div>
    </div>

    <table style="width: 100%; border-top: 1px dashed currentColor; border-bottom: 1px dashed currentColor;">
        <tr><td style="opacity: .6; padding: 4px 0; width: 40%;">No. slip</td><td>{{ $transaction->no_slip }}</td></tr>
        <tr><td style="opacity: .6; padding: 4px 0;">Tanggal</td><td>{{ \Carbon\Carbon::parse($transaction->tanggal)->translatedFormat('d F Y') }}</td></tr>
        <tr><td style="opacity: .6; padding: 4px 0;">No. rekening</td><td>{{ $transaction->account?->nomor_rekening }}</td></tr>
        <tr><td style="opacity: .6; padding: 4px 0;">Nama</td><td>{{ $transaction->account?->customer?->nama }}</td></tr>
        <tr><td style="opacity: .6; padding: 4px 0;">Jenis</td><td>{{ ucfirst($transaction->jenis_transaksi) }}</td></tr>
        <tr><td style="opacity: .6; padding: 4px 0;">Status</td><td>{{ ucfirst($transaction->status) }}</td></tr>
    </table>

    <div style="text-align: center; font-size: 22px; font-weight: 600; margin: 16px 0 4px;">
        Rp {{ number_format($transaction->jumlah_transaksi, 0, ',', '.') }}
    </div>
</div>