<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota Barang Masuk</title>
    <style>
        @page {
            size: A5 landscape;
            margin: 120px 30px 80px 30px;
        }
        

        body {
            font-family: Arial, sans-serif;
            font-size: 0.9rem;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .header {
            position: fixed;
            top: -50px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .content {
            margin: 3px;
            font-size: 0.8rem;
        }

        p {
            margin: 5px 0;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            font-size: 0.7rem;
        }

        th {
            background-color: #f2f2f2;
        }

        .footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.8rem;
        }

        .signature-table {
            width: 100%;
            text-align: center;
            margin-top: 30px;
            border: none;
        }

        .signature-table td {
            height: 50px;
            margin: 100px;
            border: none;
        }

        .new-page {
            page-break-before: always;
        }

        .page-number {
            position: absolute;
            bottom: 10px;
            right: 10px;
            font-size: 0.8rem;
        }

        .total-volume {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">NOTA PENGIRIMAN BARANG</div>
    <div class="content">
        @php
        $items_per_page = 5;
        $total_items = $stocks->count();
        $pages = ceil($total_items / $items_per_page);
        $remaining_items = $total_items % $items_per_page;
        if ($remaining_items > 7) {
            $pages++;
        }
        @endphp
<p><strong>No. BM:</strong> {{ $stocks->first()->no_bm }}</p>
<p><strong>Tanggal Kirim:</strong> {{ $stocks->first()->tgl_bm }}</p>

        @for ($page = 1; $page <= $pages; $page++)
        @php
            $start = ($page - 1) * $items_per_page;
            $end = min($start + $items_per_page, $total_items);
        @endphp
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Supplier</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stocks as $index => $stock)
                @if ($index >= $start && $index < $end)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>                            
                            <div class="flex justify-between mt-3">
                                <span>{{ $stock->barang->nama_singkat }}</span>
                                <span>({{ number_format($stock->jumlah_beli) }} {{ $stock->satuan_beli }})</span>
                            </div>
                            @if (str_contains($stock->satuan_beli, $stock->barang->satuan->nama_satuan))
                            @php
                                $t = (int)$stock->jumlah_beli;
                            @endphp
                        @else
                            @php
                                $t = (double)$stock->barang->value * (int)$stock->jumlah_beli;
                            @endphp
                        @endif
                        @if($stock->satuan_beli != $stock->barang->satuan->nama_satuan )
                            (Total {{ number_format($t) }} {{ $stock->barang->satuan->nama_satuan }} {{ ($stock->keterangan != '' || !is_null($stock->keterangan)) ? '= '.$stock->keterangan:'' }})
                        @else
                            {{ ($stock->keterangan != '' || !is_null($stock->keterangan)) ? '= '.$stock->keterangan:'' }}
                        @endif</td>
                        <td style="text-align: right">{{ number_format($stock->jumlah_beli, 0, ',', '.') }}</td>
                        <td style="text-align: center">{{ $stock->satuan_beli }}</td>
                        <td>{{ $stock->suppliers->nama }}</td>
                        <td></td>
                    </tr>
                @endif
                @endforeach
            </tbody>
        </table>

        @if ($page == $pages)
        <p><strong>Note:</strong> Silahkan Crosscheck Barang yang diterima. 
            <br>
            Nota penerimaan barang ini di scan dan di kirim ke <a href="gmail:sarana.bahagia0018@gmail.com">sarana.bahagia0018@gmail.com</a>.</p>
        <br>
        <div class="footer">
            <table class="signature-table">
                <tr>
                    <td><strong>PENERIMA</strong></td>
                    <td><strong>PENGIRIM</strong></td>
                </tr>
                <tr style="margin: 10px">
                    <td>____________________</td>
                    <td>____________________</td>
                </tr>
            </table>
            <p class="page-number">
                Halaman: {{ $page }} dari {{ $pages }}
            </p>
        </div>
        @endif

        @if ($page < $pages)
        <div class="new-page"></div>
        @endif
        @endfor
    </div>
</body>

</html>
