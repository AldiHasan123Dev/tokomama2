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
            font-family: 'Courier New', Courier, monospace;
            padding: 0;
        }

        .header {
            position: fixed;
            top: -120px;
            left: 0;
            right: 0;
            height: 0px;
            
            text-align: center;
            padding: 5px;
            box-sizing: border-box;
        }
        .content {
    margin-top: 50px;
}



        .logo {
            width: 90px;
        }

        table {
            border-collapse: collapse;
            width: 90%;
            margin:0 auto;
        }

        .border-black {
            border: 1px solid black;
            padding: 0px;
            margin-top: 20px;
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
            height: 20px; /* Same as bottom margin of @page */
           
            text-align: center;
            width: 100%;
        }

        .footer-content {
            position:fixed;
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
                    <th rowspan="4" style="width: 15%; text-align: left; margin-bottom:20px">
                        <img src="{{ public_path('tokomama.svg') }}" class="logo">
                    </th>
                    <td style="font-weight: bold">MAMA BAHAGIA</td>
                    <td></td>
                    <td>Kepada:</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Jl. Baru (Ruko Depan PLN) 
                        <br> Abepura, Jayapura </td>
                    <td></td>
                    <td>{{ $surat_jalan->customer->nama_npwp }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold">Telp: 08112692861 / 08112692859</td>
                    <td></td>
                    <td>{{ $surat_jalan->customer->alamat }}</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>{{ $ekspedisi->kota }}</td>
                </tr>
                <tr>
                    <td style="text-align: left; margin-right: 20px">SURAT JALAN</td>
                    <td colspan="2">: {{ $surat_jalan->nomor_surat }} </td>
                </tr>
                <tr class="m-5">
                    <td style="text-align: left">PO</td>
                    <td colspan="2">: {{ $surat_jalan->no_po }} </td>
                </tr>
                <tr class="mt-5">
                    <td style="text-align: left">No. Pol</td>
                    <td colspan="2">: {{ $surat_jalan->no_pol }} </td>
                </tr>
            </thead>
        </table>
    </div>
<div class="content">
    @php
    $items_per_page = 16; // Jumlah item per halaman
    $min_items_per_page = 10; // Minimal item per halaman
    $total_items = $surat_jalan->transactions->count();
    
    // Hitung jumlah halaman
    $pages = max(ceil($total_items / $items_per_page), 1);

    // Pastikan halaman terakhir memiliki minimal 10 item
    while ($pages > 1 && ($total_items % $items_per_page < $min_items_per_page)) {
        $items_per_page--;
        $pages = ceil($total_items / $items_per_page);
    }
@endphp

@for ($page = 1; $page <= $pages; $page++)
    @php
        $start = ($page - 1) * $items_per_page;
        $end = min($start + $items_per_page, $total_items);
    @endphp

        <table class="table border border-black">
            <thead>
                <tr>
                    <th class="border border-black">NO</th>
                    <th class="border border-black">JUMLAH</th>
                    <th class="border border-black">SATUAN</th>
                    <th class="border border-black">JENIS BARANG</th>
                    {{-- <th class="border border-black">TUJUAN / NAMA CUSTOMER</th> --}}
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
                        <td class="px-2" style="padding: 0px 2px">
                            
                                <span>{{ $item->barang->nama_singkat }}</span>
                                <span>({{ number_format($item->jumlah_jual) }} {{ $item->satuan_jual }})</span>
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
                        {{-- @if ($i == $start)
                            <td class="border border-black text-center" rowspan="{{ $end - $start }}">
                                {{ $surat_jalan->customer->nama && $surat_jalan->customer->nama !== '-' ? $surat_jalan->customer->nama : '-' }} <br>
                                {{ $surat_jalan->customer->kota && $surat_jalan->customer->kota !== '-' ? $surat_jalan->customer->kota : '' }} 
                            </td>
                        @endif --}}
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
            <div class="footer-content">
                <p style="margin-left: 30px;">Note &nbsp; : &nbsp; Barang diterima dalam keadaan baik dan lengkap</p>
                <table style="margin-top: 1px">
                    <tr>
                        <th style="width: 50%;"></th>
                        <th style="width: 50%; text-align: right; font-weight: normal !important; padding-right:130px">
                            {{ $surat_jalan->kota_pengirim }}, {{ date('d M Y', strtotime($surat_jalan->tgl_sj)) }}
                        </th>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <th style="font-weight: normal;">PENERIMA</th>
                        <th style="font-weight: normal;">PENGIRIM</th>
                    </tr>
                    <tr>
                        <td style="height: 13px;"></td>
                    <tr>
                        <th style="height: 30px"> </th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>({{ $surat_jalan->customer->nama }})</th>
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