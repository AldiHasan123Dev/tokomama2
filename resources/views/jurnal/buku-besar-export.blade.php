<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Buku Besar</title>
</head>
<body>
    <h1>Laporan Buku Besar</h1>

    <br>

    <p><b>Bulan : </b> @if (isset($_GET['month'])) {{ $_GET['month'] }} @else Semua  @endif</p>
    <p><b>Tahun : </b> {{ $year }}</p>
    <p><b>COA : </b> @if (isset($_GET['coa'])) {{ $coa_by_id->no_akun }} {{ $coa_by_id->nama_akun }} @else Semua COA @endif</p>

    <br>

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
                <th>Debit</th>
                <th>Credit</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($data as $item)
            @php
                if ($tipe=='D') {
                    if ($item->debit>0) {
                        $saldo_awal += $item->debit;
                    } else {
                        $saldo_awal -= $item->kredit;
                    }
                } else {
                    if ($item->debit>0) {
                        $saldo_awal -= $item->debit;
                    } else {
                        $saldo_awal += $item->kredit;
                    }
                }
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->tgl }}</td>
                <td>{{ $item->nomor }}</td>
                <td>{{ $item->coa->no_akun }}</td>
                <td>{{ $item->coa->nama_akun }}</td>
                <td>{{ $item->nopol }}</td>
                <td>{{ $item->invoice }}</td>
                <td>{{ $item->keterangan }}</td>
                <td>{{ number_format($item->debit,2,',','.') }}</td>
                <td>{{ number_format($item->kredit,2,',','.') }}</td>
                <td>{{ number_format($saldo_awal,2,',','.') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>