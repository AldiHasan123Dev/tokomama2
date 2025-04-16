<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Invoice</title>
    <style>
        @page {
            size: 21.59cm 13.97cm;
            margin: 145px 0px 50px 0px
                /* Adjust bottom margin to make space for footer */
        }

        body {
            width: 100%;
            font-size: 0.8rem;
            font-family: 'Courier New', Courier, monospace;
            margin: 0;
            padding: 0;
        }

        .header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 100px;

            text-align: center;
            padding: 5px;
            box-sizing: border-box;
        }

        table {
            border-collapse: collapse;
            width: 80%;
            margin: 0 auto;
        }

        .logo {
            max-width: 100%;
            height: 100px;
        }

        .border-black {
            border: 1px solid black;
            padding: 5px;
        }

        .py-1 {
            padding: 5px 0;
        }

        .text-center {
            text-align: center !important;
            justify-content: center;
        }

        .footer {
            position: fixed;
            margin-top: -10px;
            margin-left: 15px;
            width: 100%;
        }

        .page-break {
            page-break-before: always;
        }

        .page-number {
            position: absolute;
            align-items: bottom;
        }
    </style>
</head>

<body>
    <div class="header" style="margin-top: 10px;">
        <table style="margin-top: -40px">
            <thead>
                <tr>
                    <th rowspan="4" style="width: 15%; margin-bottom:40px;">
                        <img src="{{ public_path('tokomama.svg') }}" class="logo" style="width: 60%; height: 50%;">
                    </th>
                    <td style="font-weight: bold; font-size: 1rem;">MAMA BAHAGIA</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="font-size: 0.8rem; font-weight: bold;">Jl. Baru - Melati (Ruko depan PLN)
                        <br> Abepura, Jayapura
                    </td>
                    <td style="font-weight: bold; font-size: 1rem; text-align: center;"><u>INVOICE</u></td>
                </tr>
                <tr>
                    <td style="font-size: 0.8rem; font-weight: bold;">HP: 08112692861 / 08112692859</td>
                    <td style="text-align: center; font-size: 0.8rem">NO : {{ $invoice ?? '-' }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-align: center; font-size: 0.8rem">TOP :
                        {{ $data->first()->transaksi->suratJalan->customer->top }}</td>
                </tr>
            </thead>
        </table>
        <table class="info-table">
            <tbody>
                <tr>
                    <td class="header-cell" style="text-align:left; padding-left:40px">
                        Customer :
                        @if ($data->isNotEmpty() && optional($data->first()->transaksi)->suratJalan)
                            @php
                                $customer = optional($data->first()->transaksi->suratJalan->customer);
                            @endphp
                            {{ $customer->nama ?? '-' }}
                            ({{ $customer->no_telp ?? '-' }}) - {{ $customer->kota ?? '-' }}
                        @else
                            -
                        @endif
                    </td>

                    <td class="header-cell" style="padding-right:80px">Sales :
                        {{ $data->first()->transaksi->suratJalan->customer->sales }}</td>
                </tr>
                <tr>
                    <td style="text-align:left ;padding-left:40px; margin-top20px">
                        {{ $data->first()->transaksi->suratJalan->customer->alamat }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <main style="margin-top: 10px">
        @php
            $items_per_page = 11;
            $dates_per_page = 11;
            $total_items = $data->count();
            $total_dates = $data->pluck('transaksi.suratJalan.tgl_sj')->unique()->count();
            $pages = ceil(max($total_items / $items_per_page, $total_dates / $dates_per_page));
        @endphp

        @php
            $total = 0;

            function terbilang($angka)
            {
                $angka = (float) $angka;
                $bilangan = [
                    '',
                    'satu',
                    'dua',
                    'tiga',
                    'empat',
                    'lima',
                    'enam',
                    'tujuh',
                    'delapan',
                    'sembilan',
                    'sepuluh',
                    'sebelas',
                ];

                if ($angka < 12) {
                    return $bilangan[$angka];
                } elseif ($angka < 20) {
                    return $bilangan[$angka - 10] . ' belas';
                } elseif ($angka < 100) {
                    $hasil_bagi = (int) ($angka / 10);
                    $hasil_mod = $angka % 10;
                    return trim(sprintf('%s puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
                } elseif ($angka < 200) {
                    return 'seratus ' . terbilang($angka - 100);
                } elseif ($angka < 1000) {
                    $hasil_bagi = (int) ($angka / 100);
                    $hasil_mod = $angka % 100;
                    return trim(sprintf('%s ratus %s', $bilangan[$hasil_bagi], terbilang($hasil_mod)));
                } elseif ($angka < 2000) {
                    return 'seribu ' . terbilang($angka - 1000);
                } elseif ($angka < 1000000) {
                    $hasil_bagi = (int) ($angka / 1000);
                    $hasil_mod = $angka % 1000;
                    return trim(sprintf('%s ribu %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
                } elseif ($angka < 1000000000) {
                    $hasil_bagi = (int) ($angka / 1000000);
                    $hasil_mod = $angka % 1000000;
                    return trim(sprintf('%s juta %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
                } elseif ($angka < 1000000000000) {
                    $hasil_bagi = (int) ($angka / 1000000000);
                    $hasil_mod = fmod($angka, 1000000000);
                    return trim(sprintf('%s miliar %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
                } else {
                    return 'Angka terlalu besar';
                }
            }
        @endphp

        @for ($page = 1; $page <= $pages; $page++)
            @php
                $start_item = ($page - 1) * $items_per_page;
                $end_item = min($start_item + $items_per_page, $total_items);

                $start_date = ($page - 1) * $dates_per_page;
                $end_date = min($start_date + $dates_per_page, $total_dates);
            @endphp

            <table class="table border border-black" style="font-size: 0.7rem; ">
                <thead>
                    @for ($i = $start_item; $i < $end_item; $i++)
                    @php
                        $item = $data[$i];
                        $total += $item->harga * $item->jumlah;
                    @endphp
                    @endfor
                    <tr>
                        <th class="border border-black">No.</th>
                        <th class="border border-black">Nama Barang</th>
                        <th class="border border-black">PO</th>
                        @if ($item->transaksi->satuan_jual != $item->transaksi->barang->satuan->nama_satuan)
                        <th class="border border-black">QTY A</th>
                        <th class="border border-black">QTY B</th>
                        @else
                        <th class="border border-black">QTY X Harsat</th>
                        @endif
                        <th class="border border-black">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = $start_item; $i < $end_item; $i++)
                        @php
                            $item = $data[$i];
                            $total += $item->harga * $item->jumlah;
                        @endphp
                        <tr>
                            <td class="text-center border border-black">{{ $i + 1 }}</td>
                            <td class="border border-black">
                                {{ $item->transaksi->barang->nama }} <br>
                                @if ($item->transaksi->satuan_jual != $item->transaksi->barang->satuan->nama_satuan)
                                    (Total {{ number_format($item->jumlah * $item->transaksi->barang->value) }}
                                    {{ $item->transaksi->barang->satuan->nama_satuan }}
                                    {{ $item->transaksi->keterangan != '' || !is_null($item->transaksi->keterangan) ? '= ' . $item->transaksi->keterangan : '' }})
                                @else
                                    {{ $item->transaksi->keterangan != '' || !is_null($item->transaksi->keterangan) ? '= ' . $item->transaksi->keterangan : '' }}
                                @endif
                            </td>
                            <td class="text-center border border-black">
                                @if ($i == 0 || ($i > 0 && $i % 11 == 0))
                                    {{-- Menampilkan $no_cont pada baris pertama dan setiap kelipatan 11 --}}
                                    {{ $po ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center border border-black">{{ number_format($item->jumlah, 0, ',', '.') }}
                                {{ $item->transaksi->satuan_jual }} X
                                @if ($barang->status_ppn == 'ya')
                                    {{ number_format($item->harga * 1.11, 0, ',', '.') }}
                                @else
                                    {{ number_format($item->harga, 0, ',', '.') }}
                                @endif
                            </td>
                            @if ($item->transaksi->satuan_jual != $item->transaksi->barang->satuan->nama_satuan )
                            <td class="border border-black text-center">
                                @if ($item->transaksi->satuan_jual == 'KG' && $item->transaksi->barang->satuan->nama_satuan == 'ZAK')
                                    {{-- Tampilkan jumlah dalam satuan --}}
                                    {{ number_format($item->jumlah * $item->transaksi->barang->value) }}
                                    {{ $item->transaksi->barang->satuan->nama_satuan }}
                            
                                    {{-- Tampilkan konversi harga per KG --}}
                                    @php
                                        $totalHarga = $item->harga * $item->jumlah;
                                        $konversi = $item->jumlah * $item->transaksi->barang->value;
                                    @endphp
                            
                                    @if ($item->transaksi->barang->status_ppn == 'ya')
                                        {{ number_format(($totalHarga * 1.11) / $konversi) }}
                                    @else
                                        {{ number_format($totalHarga / $konversi) }}
                                    @endif
                                    @else

                                @endif
                            </td>
                            @endif
                            
                            <td class="border border-black" style="text-align: right;">
                                @if ($barang->status_ppn == 'ya')
                                    {{ number_format($item->harga * 1.11 * $item->jumlah, 0, ',', '.') }}
                                @else
                                    {{ number_format($item->harga * $item->jumlah, 0, ',', '.') }}
                                @endif
                            </td>
                        </tr>
                    @endfor
                    @if ($page == $pages)
                        <tr>
                            @php
                                $dpp = ($total * 11) / 12;
                                $ppn = ($barang->value_ppn / 100) * $dpp;
                            @endphp
                            @if ($item->transaksi->satuan_jual != $item->transaksi->barang->satuan->nama_satuan)
                            <td colspan="5" class="border border-black" style="text-align: right;">
                                <b>TOTAL</b>
                            </td>
                            <td class="border border-black" style="text-align: right;">
                                @if ($barang->status_ppn == 'ya')
                                    <b>{{ number_format($total * 1.11, 0, ',', '.') }}</b>
                                @else
                                    <b>{{ number_format($total, 0, ',', '.') }}</b>
                                @endif
                            </td>
                            @else
                            <td colspan="4" class="border border-black" style="text-align: right;">
                                <b>TOTAL</b>
                            </td>
                            <td class="border border-black" style="text-align: right;">
                                @if ($barang->status_ppn == 'ya')
                                    <b>{{ number_format($total * 1.11, 0, ',', '.') }}</b>
                                @else
                                    <b>{{ number_format($total, 0, ',', '.') }}</b>
                                @endif
                            </td>
                            @endif
                            {{-- <td colspan="5"  style="text-align: right;">
                        Subtotal
                        <br>
                        DPP 11/12
                        <br>
                        @if ($barang->status_ppn == 'ya')
                        PPN 12%
                        @else
                        PPN 12% (DIBEBASKAN)
                        @endif
                    </td>
                    <td class="border border-black" style="text-align: right;" >
                        @php
                            $dpp = $total * 11/12;
                        @endphp
                    {{ number_format($total, 0, ',', '.') }}
                    <br>
                        @if ($barang->status_ppn == 'ya')
                        {{ number_format($dpp, 0, ',', '.') }}
                    @else
                        -
                    @endif
                    <br>
                    @if ($barang->status_ppn == 'ya')
                    @php
                        $ppn = ($barang->value_ppn / 100) * $dpp
                    @endphp
                        {{ number_format($ppn, 0, ',', '.') }}
                    @else
                        -
                    @endif
                    </td> --}}
                        </tr>
                        {{-- <tr> --}}
                        {{-- <td colspan="5" class="border border-black" style="text-align: right;">
                        <b>TOTAL</b>
                    </td>
                    <td class="border border-black" style="text-align: right;" >
                    @if ($barang->status_ppn == 'ya')
                        <b>{{ number_format($ppn + $total, 0, ',', '.') }}</b>
                    @else
                        <b>{{ number_format($total, 0, ',', '.') }}</b>
                    @endif
                    </td> --}}
                        {{-- </tr> --}}
                </tbody>
        @endif
        </table>
        <div class="footer">
            @if ($page == $pages)
                <p style="font-weight: bold;padding-left:30px; font-size: 0.8rem">
                    Terbilang:
                    @if ($barang->status_ppn == 'ya')
                        {{ ucwords(strtolower(terbilang(round($total * 1.11)))) }} Rupiah
                    @else
                        {{ ucwords(strtolower(terbilang(round($total)))) }} Rupiah
                    @endif
                </p>
                <table style="font-size: 0.8rem;">
                    <tr>
                        <th style="text-align: left; padding-right: 50px; font-style: italic;">Pembayaran ke rekening:
                        </th>
                        <td style="padding-left: 40px;">Penerima</td>
                        <td style="align-items:left ;text-align: center;">Surabaya, {{ $formattedDate }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding-right: 50px; font-style: italic;">CV. SARANA BAHAGIA</th>
                        <td></td>
                        <td style="text-align: center;">Hormat Kami</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding-right: 50px; font-style: italic;"> Bank Mandiri <br>
                            14.000.45006.005</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding-left: 50px;"></th>
                        <td style="padding-top:30px;">_____________________</td>
                        <th style="padding-top:30px">(MAMA BAHAGIA)</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td>TD. Tgn & Nama Terang</td>
                        <td></td>
                    </tr>
                </table>
            @endif

        </div>
        <p class="page-number"
            style=" position: fixed; align-items:bottom ; left: 10px; bottom: -20px; margin: 0; font-size: 0.8rem;">Hal:
            {{ $page }} dari {{ $pages }}</p>
        @if ($page < $pages)
            <div class="page-break"></div>
        @endif
        @endfor
    </main>
</body>

</html>
