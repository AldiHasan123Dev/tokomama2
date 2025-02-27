<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ isset($coa) ? $coa->no_akun  : 'Laporan Buku Besar' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            text-align: center;
        }
        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
        tbody tr:nth-child(even) {
            background-color: #fff;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>Laporan Buku Besar</h1>

    <p><b>Bulan:</b> {{ request('month', 'Semua') }}</p>
    <p><b>Tahun:</b> {{ $year }}</p>
    <p><b>COA:</b> {{ isset($coa) ? "{$coa->no_akun} - {$coa->nama_akun}" : 'Semua COA' }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>No. Jurnal</th>
                <th>No. Akun</th>
                <th>Akun</th>
                <th>Nopol</th>
                <th>Invoice</th>
                <th>Keterangan</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Kredit</th>
                <th class="text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
        @php
            $currentSaldo = $saldo_awal;
            // Cek apakah nama akun COA mengandung kata 'Biaya' atau 'Pendapatan'
            $isBiayaPendapatan = isset($coa) && in_array(substr($coa->no_akun, 0, 1), ['5', '6']);
            if ($isBiayaPendapatan) {
                $currentSaldo = 0;
            }
        @endphp

        {{-- Jika nama akun bukan "Biaya" atau "Pendapatan", tampilkan saldo awal --}}
        @if (!$isBiayaPendapatan)
            <tr>
                <td colspan="10"><b>Saldo Awal</b></td>
                <td class="text-right"><b>{{ $currentSaldo }}</b></td>
            </tr>
        @endif
        
        @foreach ($data as $item)
        @php
                   if ($tipe == 'D') {
        // Menambahkan debit ke saldo yang sudah ada jika debit lebih besar dari 0
        if ($item->debit > 0) {
            $currentSaldo += $item->debit;
        } else {
            // Debit kurang dari atau sama dengan 0, maka kredit ditambahkan
            $currentSaldo -= $item->kredit;
        }
    } else {
        // Jika tipe bukan 'D', saldo tetap dihitung dengan cara yang sama
        if ($item->debit > 0) {
            $currentSaldo -= $item->debit;
        } else {
            $currentSaldo += $item->kredit;
        }
    }
            @endphp
    
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->tgl }}</td>
                <td>{{ $item->nomor }}</td> 
                <td>{{ $item->coa->no_akun ?? '-' }}</td>
                <td>{{ $item->coa->nama_akun ?? '-' }}</td>
                <td>{{ $item->nopol ?? '-' }}</td>
                <td>{{ $item->invoice ?? '-' }}</td>
                <td>{{ $item->keterangan }}</td>
                <td class="text-right">{{ $item->debit }}</td>
                <td class="text-right">{{ $item->kredit }}</td>
                <td class="text-right">{{ $currentSaldo }}</td>
                
                {{-- Jika akun adalah "Biaya" atau "Pendapatan", jangan tampilkan saldo --}}
                {{-- @if (!$isBiayaPendapatan)
                    <td class="text-right">{{ number_format($currentSaldo, 2) }}</td>
                @else
                    <td class="text-right">0</td>
                @endif --}}
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
