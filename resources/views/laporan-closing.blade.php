<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penutupan Kas</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 16mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #111;
            margin: 0;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            padding-bottom: 10px;
            margin-bottom: 18px;
            border-bottom: 2px solid #111;
        }

        .header h2 {
            font-size: 16pt;
            margin: 0 0 5px;
            letter-spacing: 0.5px;
        }

        .header p {
            margin: 0;
            font-size: 10pt;
        }

        .identity {
            margin-bottom: 18px;
            font-size: 10pt;
        }

        .identity div {
            display: flex;
            margin-bottom: 4px;
        }

        .identity span:first-child {
            width: 145px;
        }

        .identity span:nth-child(2) {
            width: 15px;
        }

        .section {
            margin-top: 16px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 10.5pt;
            font-weight: bold;
            padding: 6px 8px;
            background: #eaeaea;
            border: 1px solid #333;
            margin-bottom: 7px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        th, td {
            border: 1px solid #555;
            padding: 6px 8px;
            vertical-align: middle;
        }

        th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .number {
            text-align: right;
            white-space: nowrap;
        }

        .center {
            text-align: center;
        }

        .total-row td {
            font-weight: bold;
            background: #f0f0f0;
        }

        .result-table td:first-child {
            width: 55%;
        }

        .result-highlight {
            font-weight: bold;
            font-size: 11pt;
        }

        .sub-title {
            font-weight: bold;
            margin: 10px 0 5px;
        }

        .page-break {
            page-break-before: always;
        }

        .signature-area {
            display: flex;
            justify-content: space-between;
            text-align: center;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature {
            width: 42%;
        }

        .signature .space {
            height: 70px;
        }

        .signature .name {
            font-weight: bold;
            text-decoration: underline;
        }

        .print-button {
            display: block;
            margin: 20px auto;
            padding: 9px 22px;
            border: none;
            background: #222;
            color: white;
            cursor: pointer;
            font-size: 10pt;
            border-radius: 4px;
        }

        @media print {
            .print-button {
                display: none;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
@php
    \Carbon\Carbon::setLocale('id');

    $formatRupiah = fn ($nilai) =>
        'Rp ' . number_format((float) $nilai, 0, ',', '.');

    $formatTanggal = $closing->created_at
        ? $closing->created_at->translatedFormat('d F Y')
        : '-';

    $koin = [100, 200, 500, 1000];
    $kertas = [1000, 2000, 5000, 10000, 20000, 50000, 100000];

    $ambilDetail = function ($jenis, $nominal) use ($closing) {
        $detail = $closing->closingDetails->first(
            fn ($item) =>
                $item->jenis_pecahan === $jenis
                && (int) $item->nominal_pecahan === $nominal
        );

        return [
            'jumlah' => (int) ($detail?->jumlah_pecahan ?? 0),
            'subtotal' => (float) ($detail?->subtotal ?? 0),
        ];
    };

    $totalKoin = 0;
    $totalKertas = 0;
    $jumlahKoin = 0;
    $jumlahKertas = 0;

    foreach ($koin as $nominal) {
        $detail = $ambilDetail('koin', $nominal);
        $totalKoin += $detail['subtotal'];
        $jumlahKoin += $detail['jumlah'];
    }

    foreach ($kertas as $nominal) {
        $detail = $ambilDetail('kertas', $nominal);
        $totalKertas += $detail['subtotal'];
        $jumlahKertas += $detail['jumlah'];
    }

    $totalFisik = $totalKoin + $totalKertas;
    $selisih = (float) $closing->selisih;
    $statusCocok = abs($selisih) < 0.01;
@endphp

    {{-- HALAMAN 1 --}}
    <div class="header">
        <h2>LAPORAN PENUTUPAN KAS</h2>
        <p>SMKS 1 PARAHYANGAN & SMP PARAHYANGAN</p>
    </div>

    <div class="identity">
        <div>
            <span>Tanggal</span>
            <span>:</span>
            <span>{{ $formatTanggal }}</span>
        </div>

        <div>
            <span>Petugas</span>
            <span>:</span>
            <span>{{ $closing->user?->name ?? '-' }}</span>
        </div>

        <div>
            <span>Penanggung Jawab</span>
            <span>:</span>
            <span>{{ $closing->approverUser?->name ?? '-' }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">A. REKAP SISTEM</div>

        <table>
            <thead>
                <tr>
                    <th style="width: 65%">Keterangan</th>
                    <th>Jumlah</th>
                    <th>Nominal</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Transaksi Setoran</td>
                    <td class="center">{{ $rekap['jumlah_setoran'] }}</td>
                    <td class="number">
                        {{ $formatRupiah($rekap['total_setoran']) }}
                    </td>
                </tr>

                <tr>
                    <td>Transaksi Penarikan</td>
                    <td class="center">{{ $rekap['jumlah_penarikan'] }}</td>
                    <td class="number">
                        {{ $formatRupiah($rekap['total_penarikan']) }}
                    </td>
                </tr>

                <tr>
                    <td>Transaksi Void yang Disetujui</td>
                    <td class="center">{{ $rekap['jumlah_void'] }}</td>
                    <td class="number">
                        {{ $formatRupiah($rekap['total_void']) }}
                    </td>
                </tr>

                <tr class="total-row">
                    <td colspan="2">Saldo Kas Seharusnya</td>
                    <td class="number">
                        {{ $formatRupiah($closing->total_sistem) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">B. HASIL PENCOCOKAN</div>

        <table class="result-table">
            <tbody>
                <tr>
                    <td>Saldo Sistem</td>
                    <td class="number">
                        {{ $formatRupiah($closing->total_sistem) }}
                    </td>
                </tr>

                <tr>
                    <td>Jumlah Uang Fisik</td>
                    <td class="number">
                        {{ $formatRupiah($totalFisik) }}
                    </td>
                </tr>

                <tr>
                    <td>Selisih Kas</td>
                    <td class="number result-highlight">
                        {{ $formatRupiah(abs($selisih)) }}
                    </td>
                </tr>

                <tr class="total-row">
                    <td>Status</td>
                    <td class="center result-highlight">
                        {{ $statusCocok ? 'COCOK' : 'SELISIH' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- HALAMAN 2 --}}
    <div class="page-break"></div>

    <div class="header">
        <h2>LAPORAN PENUTUPAN KAS</h2>
        <p>SMKS 1 PARAHYANGAN & SMP PARAHYANGAN</p>
    </div>

    <div class="identity">
        <div>
            <span>Tanggal</span>
            <span>:</span>
            <span>{{ $formatTanggal }}</span>
        </div>

        <div>
            <span>Petugas</span>
            <span>:</span>
            <span>{{ $closing->user?->name ?? '-' }}</span>
        </div>

        <div>
            <span>Penanggung Jawab</span>
            <span>:</span>
            <span>{{ $closing->approverUser?->name ?? '-' }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">C. HITUNG UANG FISIK</div>

        <div class="sub-title">1. Uang Koin</div>

        <table>
            <thead>
                <tr>
                    <th>Nominal Koin</th>
                    <th>Jumlah Keping</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($koin as $nominal)
                    @php
                        $detail = $ambilDetail('koin', $nominal);
                    @endphp

                    <tr>
                        <td class="number">
                            {{ $formatRupiah($nominal) }}
                        </td>
                        <td class="center">
                            {{ $detail['jumlah'] }}
                        </td>
                        <td class="number">
                            {{ $formatRupiah($detail['subtotal']) }}
                        </td>
                    </tr>
                @endforeach

                <tr class="total-row">
                    <td>Total Koin</td>
                    <td class="center">{{ $jumlahKoin }}</td>
                    <td class="number">
                        {{ $formatRupiah($totalKoin) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="sub-title">2. Uang Kertas</div>

        <table>
            <thead>
                <tr>
                    <th>Nominal Kertas</th>
                    <th>Jumlah Lembar</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($kertas as $nominal)
                    @php
                        $detail = $ambilDetail('kertas', $nominal);
                    @endphp

                    <tr>
                        <td class="number">
                            {{ $formatRupiah($nominal) }}
                        </td>
                        <td class="center">
                            {{ $detail['jumlah'] }}
                        </td>
                        <td class="number">
                            {{ $formatRupiah($detail['subtotal']) }}
                        </td>
                    </tr>
                @endforeach

                <tr class="total-row">
                    <td>Total Kertas</td>
                    <td class="center">{{ $jumlahKertas }}</td>
                    <td class="number">
                        {{ $formatRupiah($totalKertas) }}
                    </td>
                </tr>

                <tr class="total-row">
                    <td colspan="2">TOTAL UANG FISIK</td>
                    <td class="number">
                        {{ $formatRupiah($totalFisik) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

        {{-- TANDA TANGAN --}}
    <div class="signature-area">
        <div class="signature">
            <div>Petugas</div>
            <div class="space"></div>
            <div class="name">
                {{ $closing->user?->name ?? '........................' }}
            </div>
    </div>

    <div class="signature">
        <div>Penanggung Jawab</div>
        <div class="space"></div>
        <div class="name">
            {{ $closing->approverUser?->name ?? '........................' }}
        </div>
    </div>
</div>

    <button class="print-button" onclick="window.print()">
        Cetak Laporan
    </button>
</body>
</html>
