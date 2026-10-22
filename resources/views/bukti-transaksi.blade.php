<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Bukti Transaksi {{ $transaction->no_slip }}</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 380px; margin: 24px auto; font-size: 14px; }
        h2, p.sub { text-align: center; margin: 0; }
        p.sub { color: #555; margin-bottom: 16px; }
        table { width: 100%; border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 8px 0; }
        td { padding: 3px 0; vertical-align: top; }
        td:first-child { color: #555; width: 40%; }
        .total { font-size: 18px; font-weight: bold; text-align: center; margin: 16px 0; }
        .ttd { display: flex; justify-content: space-between; margin-top: 32px; text-align: center; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <h2>Tabungan Sekolah</h2>
    <p class="sub">Bukti Transaksi</p>

    <table>
        <tr><td>No. Slip</td><td>{{ $transaction->no_slip }}</td></tr>
        <tr><td>Tanggal</td><td>{{ \Carbon\Carbon::parse($transaction->tanggal)->translatedFormat('d F Y') }}</td></tr>
        <tr><td>No. Rekening</td><td>{{ $transaction->account?->nomor_rekening }}</td></tr>
        <tr><td>Nama</td><td>{{ $transaction->account?->customer?->nama }}</td></tr>
        <tr><td>Jenis</td><td>{{ ucfirst($transaction->jenis_transaksi) }}</td></tr>
        <tr><td>Status</td><td>{{ ucfirst($transaction->status) }}</td></tr>
    </table>

    <div class="total">Rp {{ number_format($transaction->jumlah_transaksi, 0, ',', '.') }}</div>

    <div class="ttd">
        <div>Nasabah<br><br><br>(__________)</div>
        <div>Petugas<br><br><br>({{ $transaction->user?->name }})</div>
    </div>

    <p class="no-print" style="text-align:center;margin-top:24px">
        <button onclick="window.print()">Cetak</button>
    </p>
</body>
</html>