<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $surat_jalan->nomor_surat }}</title>
    <style>
        @page {
            size: 21.59cm 13.97cm;
            margin: 145px 0px 50px 0px /* Adjust bottom margin to make space for footer */
        }

        body {
            width: 100%;
            font-size: 0.8rem;
            margin: 0;
            padding: 0;
        }

        .header {
            position: fixed;
            top: -130px;
            left: 0;
            right: 0;
            height: 0px;

            text-align: center;
            padding: 5px;
            box-sizing: border-box;
        }

        .content {
            height: 20px;
        }

        .logo {
            width: 70px;
        }

        table {
            border-collapse: collapse;
            width: 90%;
            margin:0 auto;
        }

        .border-black {
            border: 1px solid black;
            padding: 0px;
        }

        .text-center {
            text-align: center;
            justify-content: center;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 66px; /* Same as bottom margin of @page */
           
            text-align: center;
            width: 100%;
        }

        .footer-content {
            position:fixed;
            bottom: -1px;
            padding-top:14px;
            margin-left:15px;
            width:100%;
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
    <div class="header">
        <table>
            <thead>
                <tr>
                    <th rowspan="4" style="width: 20%">
                        <img src="{{ public_path('logo_sb.svg') }}" class="logo">
                    </th>
                    <td>TOKO MAMA</td>
                    <td></td>
                    <td>Kepada:</td>
                </tr>
                <tr>
                    <td>Jl. Kalianak 55 Blok G, Surabaya</td>
                    <td></td>
                    <td>{{ $surat_jalan->kepada }}</td>
                </tr>
                <tr>
                    <td>Telp: 031-7495507</td>
                    <td></td>
                    <td>{{ $ekspedisi->alamat }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>{{ $ekspedisi->kota }}</td>
                </tr>
                <tr>
                    <th>SURAT JALAN</th>
                    <td style="font-weight: bold" colspan="2">No: {{ $surat_jalan->nomor_surat }} </td>
                </tr>
                <tr class="m-5">
                    <th>PO</th>
                    <td style="font-weight: bold" colspan="2">No: {{ $surat_jalan->no_po }} </td>
                </tr>
                <tr class="mt-5">
                    <th>No. Pol</th>
                    <td style="font-weight: bold" colspan="2">No: {{ $surat_jalan->no_pol }} </td>
                </tr>
            </thead>
        </table>
    </div>

<div class="content">
    @php
        $items_per_page = 16; // Mengubah jumlah item per halaman menjadi 16
        $total_items = $surat_jalan->transactions->count();
        
        // Calculate the number of pages
        $pages = ceil($total_items / $items_per_page);

        // Calculate remaining items after the first page
        $remaining_items = $total_items % $items_per_page;

        // Sesuaikan jumlah halaman jika halaman terakhir memiliki lebih dari 7 item
        if ($remaining_items > 7) {
            $pages++;
        }
    @endphp

    @for ($page = 1; $page <= $pages; $page++)
        @php
            if ($page < $pages) {
                // Halaman pertama dan tengah mengambil item secara normal
                $start = ($page - 1) * $items_per_page;
                $end = min($start + $items_per_page, $total_items);

              
                if ($page == $pages - 1 && $remaining_items > 7) {
                    $end = $total_items - 1;
                }
            } else {
                // Halaman terakhir, jika ada lebih dari 7 item tersisa, hanya mengambil item terakhir
                if ($remaining_items > 7) {
                    $start = $total_items - 1;
                    $end = $total_items;
                } else {
                    // Jika sisa item kurang dari atau sama dengan 7, ambil semua sisa item
                    $start = $total_items - $remaining_items;
                    $end = $total_items;
                }
            }
        @endphp
        <br>
        <table class="table border border-black">
            <thead>
                <tr>
                    <th class="border border-black">NO</th>
                    <th class="border border-black">JUMLAH</th>
                    <th class="border border-black">SATUAN</th>
                    <th class="border border-black">JENIS BARANG</th>
                    <th class="border border-black">TUJUAN / NAMA CUSTOMER</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = $start; $i < $end; $i++)
                    @php
                        $item = $surat_jalan->transactions[$i];
                    @endphp
                    <tr>
                        <td class="text-center" style="vertical-align: top; border-right: 1px solid black">
                            <span>{{ $i + 1 }}</span><br>
                        </td>
                        <td class="text-center" style="vertical-align: top; border-right: 1px solid black">
                            <span>{{ number_format($item->jumlah_jual) }}</span>
                        </td>
                        <td class="text-center" style="vertical-align: top; border-right: 1px solid black">
                            <span>{{ $item->satuan_jual }}</span><br>
                        </td>
                        <td class="px-2" style="padding: 0px 5px">
                            <div class="flex justify-between mt-3">
                                <span>{{ $item->barang->nama_singkat }}</span>
                                <span>({{ number_format($item->jumlah_jual) }} {{ $item->satuan_jual }})</span>
                            </div>
                            @if (str_contains($item->satuan_jual, $item->barang->satuan->nama_satuan))
                                @php
                                    $t = (int)$item->jumlah_jual;
                                @endphp
                            @else
                                @php
                                    $t = (double)$item->barang->value * (int)$item->jumlah_jual;
                                @endphp
                            @endif
                            @if($item->satuan_jual != $item->barang->satuan->nama_satuan )
                            @php
                            @endphp
                                (Total {{ number_format($t) }} {{ $item->barang->satuan->nama_satuan }} {{ ($item->keterangan != '' || !is_null($item->keterangan)) ? '= '.$item->keterangan:'' }})
                            @else
                                {{ ($item->keterangan != '' || !is_null($item->keterangan)) ? '= '.$item->keterangan:'' }}
                            @endif
                        </td>
                        @if ($i == $start)
                            <td class="border border-black text-center" rowspan="{{ $end - $start }}">
                                {{ $surat_jalan->customer->nama && $surat_jalan->customer->nama !== '-' ? $surat_jalan->customer->nama : '-' }} <br>
                                {{ $surat_jalan->customer->kota && $surat_jalan->customer->kota !== '-' ? $surat_jalan->customer->kota : '' }} 
                            </td>
                        @endif
                    </tr>
                @endfor
            </tbody>
        </table>

        <div class="footer">
            <p class="page-number" style="align-items:bottom; right: 10px; bottom: -40px; margin: 0; font-size: 0.8rem;">
                Halaman: {{ $page }} dari {{ $pages }}
            </p>
        </div>

        @if ($page == $pages)
            <div class="footer-content" style="margin-bottom: 70px">
                <p style="margin-left: 30px;">Note &nbsp; : &nbsp; Barang yang diterima dalam keadaan baik dan lengkap</p>
                <table>
                    <tr>
                        <th style="width: 50%;"></th>
                        <th style="width: 50%; text-align: right; font-weight: normal !important; padding-right:120px">
                            {{ $surat_jalan->kota_pengirim }}, {{ date('d M Y', strtotime($surat_jalan->tgl_sj)) }}
                        </th>
                    </tr>
                    <tr>
                        <td style="height: 10px"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th><b>PENERIMA</b></th>
                        <th><b>PENGIRIM</b></th>
                    </tr>
                    <tr>
                        <td style="height: 15px;"></td>
                    <tr>
                        <th style="height: 30px"> </th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>({{ $surat_jalan->nama_penerima }})</th>
                        <th>({{ $surat_jalan->nama_pengirim }})</th>
                    </tr>
                </table>
            </div>
        @endif

        @if ($page < $pages)
            <div class="page-break"></div>
        @endif
    @endfor
</div>
</body>

</html>