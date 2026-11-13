@php
    \Carbon\Carbon::setLocale('id');
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penutupan Kas Harian</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #000;
            background: #e5e7eb;
            margin: 0;
            padding: 30px 0;
        }

        .paper {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 25px;
            padding: 12mm;
            background: white;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
        }

        h2, h3, p {
            margin: 0;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .header h2 {
            font-size: 20px;
        }

        .header p {
            margin-top: 5px;
        }

        .info {
            margin-bottom: 15px;
            line-height: 1.8;
        }

        .info table {
            width: 100%;
            border-collapse: collapse;
        }

        .info td {
            padding: 4px;
        }

        .section-title {
            font-size: 12px;
            margin: 12px 0 7px;
            break-after: avoid;
            page-break-after: avoid;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            table-layout: fixed;
        }

        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 5px;
            overflow-wrap: break-word;
        }

        table.data th {
            background: #eee;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            font-weight: bold;
        }

        .summary td {
            padding: 7px;
        }

        .signatures {
            display: flex;
            justify-content: space-around;
            text-align: center;
            margin-top: 35px;
            padding-top: 0;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .signature {
            width: 40%;
            break-inside: avoid;
            page-break-inside: avoid;
        }

        .signature-space {
            height: 65px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .page-title {
            text-align: center;
            margin-bottom: 18px;
        }

        .page-title h3 {
            font-size: 15px;
            margin-bottom: 5px;
        }

        .page-info {
            text-align: left;
            margin-bottom: 18px;
            line-height: 1.8;
        }

        .page-number {
            text-align: right;
            margin-top: auto;
            padding-top: 20px;
            font-size: 9px;
        }

        .no-print {
            text-align: center;
            margin: 25px 0;
        }

        button {
            padding: 10px 25px;
            cursor: pointer;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .paper {
                width: auto;
                min-height: 0;
                height: 273mm;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .page-one {
                break-after: page;
                page-break-after: always;
            }

            .page-two {
                break-before: page;
                page-break-before: always;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <!-- HALAMAN 1: DATA CLOSING DAN TRANSAKSI -->

    <div class="paper page-one">

        <div class="header">
            <h2>TABUNGAN SEKOLAH</h2>
            <p>LAPORAN PENUTUPAN KAS HARIAN</p>
            <p>Bukti Penutupan Kas</p>
        </div>

        <div class="info">
            <table>
                <tr>
                    <td width="25%">Instansi Sekolah</td>
                    <td>: SMKS 1 PARAHYANGAN & SMP PARAHYANGAN</td>
                </tr>
                <tr>
                    <td>Hari / Tanggal</td>
                    <td>: {{ $closing->created_at->translatedFormat('l, d F Y') }}</td>
                </tr>
                <tr>
                    <td>Petugas</td>
                    <td>: {{ $closing->user?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td>Pemeriksa</td>
                    <td>: {{ $closing->approverUser?->name ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <h3 class="section-title">A. Rincian Uang Masuk</h3>

        <table class="data">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 12%;">No. Slip</th>
                    <th style="width: 17%;">Tanggal</th>
                    <th style="width: 20%;">Nama Nasabah</th>
                    <th style="width: 16%;">No. Rekening</th>
                    <th style="width: 12%;">Jenis</th>
                    <th style="width: 18%;">Jumlah</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($transactions as $index => $transaction)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $transaction->no_slip }}</td>
                        <td>
                            {{ $transaction->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            {{ $transaction->account?->customer?->nama ?? '-' }}
                        </td>
                        <td>
                            {{ $transaction->account?->nomor_rekening ?? '-' }}
                        </td>
                        <td>{{ ucfirst($transaction->jenis_transaksi) }}</td>
                        <td class="text-right">
                            Rp {{ number_format($transaction->jumlah_transaksi, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            Tidak ada transaksi setoran.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr class="total-row">
                    <td colspan="6" class="text-right">
                        Total Seluruh Uang Masuk
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($transactions->sum('jumlah_transaksi'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <h3 class="section-title">B. Rekapitulasi Kas</h3>

        <table class="data summary">
            <tr>
                <td>Total Uang Sistem</td>
                <td class="text-right">
                    Rp {{ number_format($closing->total_sistem, 0, ',', '.') }}
                </td>
            </tr>

            <tr>
                <td>Saldo Kas Fisik</td>
                <td class="text-right">
                    Rp {{ number_format($closing->total_fisik, 0, ',', '.') }}
                </td>
            </tr>

            <tr class="total-row">
                <td>Selisih Kas</td>
                <td class="text-right">
                    Rp {{ number_format($closing->selisih, 0, ',', '.') }}
                </td>
            </tr>
        </table>

        <div class="signatures">
            <div class="signature">
                Petugas
                <div class="signature-space"></div>
                <div class="signature-name">
                    {{ $closing->user?->name ?? '________________' }}
                </div>
            </div>

            <div class="signature">
                Guru Penanggung Jawab
                <div class="signature-space"></div>
                <div class="signature-name">
                    {{ $closing->approverUser?->name ?? '________________' }}
                </div>
            </div>
        </div>

        <div class="page-number">
            Halaman 1 dari 2
        </div>

    </div>


    <!-- HALAMAN 2: RINCIAN PECAHAN UANG -->

    <div class="paper page-two">

        <div class="header">
            <h2>TABUNGAN SEKOLAH</h2>
            <p>LAMPIRAN LAPORAN PENUTUPAN KAS</p>
        </div>

        <div class="page-title">
            <h3>RINCIAN PECAHAN UANG</h3>
        </div>

        <div class="page-info">
            <div>
                Hari / Tanggal:
                {{ $closing->created_at->translatedFormat('l, d F Y') }}
            </div>

            <div>
                Petugas: {{ $closing->user?->name ?? '-' }}
            </div>
        </div>

        <h3 class="section-title">C. Rincian Pecahan Uang Koin</h3>

        <table class="data">
            <thead>
                <tr>
                    <th>Nominal</th>
                    <th>Jumlah Keping</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>
                @foreach ([100, 200, 500, 1000] as $nominal)
                    @php
                        $detail = $closing->closingDetails->first(
                            fn ($item) =>
                                $item->jenis_pecahan === 'koin'
                                && (int) $item->nominal_pecahan === $nominal
                        );

                        $jumlah = (int) ($detail?->jumlah_pecahan ?? 0);
                    @endphp

                    <tr>
                        <td class="text-right">
                            Rp {{ number_format($nominal, 0, ',', '.') }}
                        </td>
                        <td class="text-center">{{ $jumlah }}</td>
                        <td class="text-right">
                            Rp {{ number_format($nominal * $jumlah, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-right">Total Koin</td>
                    <td class="text-right">
                        Rp {{ number_format(
                            $closing->closingDetails
                                ->where('jenis_pecahan', 'koin')
                                ->sum('subtotal'),
                            0, ',', '.'
                        ) }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <h3 class="section-title">D. Rincian Pecahan Uang Kertas</h3>

        <table class="data">
            <thead>
                <tr>
                    <th>Nominal</th>
                    <th>Jumlah Lembar</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>
                @foreach ([1000, 2000, 5000, 10000, 20000, 50000, 100000] as $nominal)
                    @php
                        $detail = $closing->closingDetails->first(
                            fn ($item) =>
                                $item->jenis_pecahan === 'kertas'
                                && (int) $item->nominal_pecahan === $nominal
                        );

                        $jumlah = (int) ($detail?->jumlah_pecahan ?? 0);
                    @endphp

                    <tr>
                        <td class="text-right">
                            Rp {{ number_format($nominal, 0, ',', '.') }}
                        </td>
                        <td class="text-center">{{ $jumlah }}</td>
                        <td class="text-right">
                            Rp {{ number_format($nominal * $jumlah, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr class="total-row">
                    <td colspan="2" class="text-right">
                        Total Uang Kertas
                    </td>
                    <td class="text-right">
                        Rp {{ number_format(
                            $closing->closingDetails
                                ->where('jenis_pecahan', 'kertas')
                                ->sum('subtotal'),
                            0, ',', '.'
                        ) }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="page-number">
            Halaman 2 dari 2
        </div>

    </div>

    <div class="no-print">
        <button onclick="window.print()">
            Cetak / Simpan PDF
        </button>
    </div>

</body>
</html>