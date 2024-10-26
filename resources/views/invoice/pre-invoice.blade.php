<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Preview Invoice</x-slot:tittle>
        <div class="header" style="margin-top:10px">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>
                            <img src="{{ asset('logo_sb.svg') }}" class="logo" style="width: 20%; height: 20%; margin:10px">
                        </th>
                        <td style="font-weight: bold; font-size: 1.2rem; text-align:center;">CV. SARANA BAHAGIA</td>
                        
                    </tr>
                    <tr>
                        <td style="font-size: 1rem; margin:2px">Jl. Kalianak 55 Blok G, Surabaya</td>
                        <td style="font-weight: bold; font-size: 1.2rem; text-align: center;"><u>PREVIEW INVOICE</u></td>
                    </tr>
                    <tr>
                        <td style="font-size: 1rem;">Telp: 031-7495507</td>
                        <td style="text-align: center; font-size: 1rem">NO : {{ $inv ?? '-' }}</td>
                    </tr>
                </thead>
            </table>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <tbody>
                    <tr>
                        <td class="header-cell" style="font-size: 1rem; text-align:left">Customer :
                            {{ $transaksi->suratJalan->customer->nama ?? '-' }}</td>
                        <td class="header-cell" style="text-align: center; font-size: 1rem;">KAPAL
                            : {{ $transaksi->suratJalan->nama_kapal ?? '-' }}</td>

                    </tr>
                    <tr>
                        <td class="header-cell" style="text-align:left;font-size: 1rem;">PO :
                            {{ $transaksi->suratJalan->no_po ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <main>
            <br>

            @php
                $items_per_page = 11;
                $dates_per_page = 11;
                $total_items = count($data);
                $total_dates = count(array_unique(array_column($data, 'transaksi.suratJalan.tgl_sj')));

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

<table style="width: 100%;  border: solid; border-collapse: collapse; margin-top: 20px;" border="1">
                    <thead>
                        <tr>
                            <th style="text-align: center; border:solid; padding: 8px;">No.</th>
                            <th style="text-align: center;  border:solid; padding: 8px;">Tgl Barang Masuk</th>
                            <th style="text-align: center;  border:solid; padding: 8px;">Nama Barang</th>
                            <th style="text-align: center;  border:solid; padding: 8px;">No-Cont</th>
                            <th style="text-align: center;  border:solid; padding: 8px;">Quantity</th>
                            <th style="text-align: center;  border:solid; padding: 8px;">Harga Satuan(Rp)</th>
                            <th style="text-align: center;  border:solid; padding: 8px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_all = 0;
                            $index = 1;
                        @endphp
                        @foreach ($data as $id_transaksi => $items)
                            @if (isset($items['invoice']))
                                @foreach ($items['invoice'] as $idx => $invoice)
                                    @php
                                        $jumlah = $items['jumlah'][$idx] ?? 0; // Cegah error jika jumlah tidak ada
                                        $harga = $invoice['harga'] ?? 0; // Cegah error jika harga tidak ada
                                        $total += $items['harga_jual'][$idx] * $items['jumlah'][$idx];
                                        $total_all += $total;
                                    @endphp
                                    <tr>
                                        <td style="border:solid; text-align: center; padding:2px;">{{ $index++ }}</td>
                                        <td style=" border:solid; text-align: center; padding: 8px;">{{ $items['tgl_sj'][$idx] }}</td>
                                        <td style=" border:solid; text-align: center;">
                                            {{ $items['nama_barang'][$idx] }}
                                            ({{ ($items['jumlah_jual'][$idx]) }} {{ $items['satuan_jual'][$idx] }})
                                            @if (str_contains($items['satuan_jual'][$idx], $items['nama_satuan'][$idx]))
                                                @php
                                                    $t = (int)$items['jumlah_jual'][$idx];
                                                @endphp
                                            @else
                                                @php
                                                    $t = (double)$items['value'][$idx] * (int)$items['jumlah_jual'][$idx];
                                                @endphp
                                            @endif
                                            
                                            
                                            @if($items['satuan_jual'][$idx] != $items['nama_satuan'][$idx])
                                                (Total {{ number_format($t) }} {{ $items['nama_satuan'][$idx] }} {{ ($items['keterangan'][$idx] != '' || !is_null($items['keterangan'][$idx])) ? '= '.$items['keterangan'][$idx]:'' }})
                                            @else
                                                {{ ($items['keterangan'][$idx] != '' || !is_null($items['keterangan'][$idx])) ? '= '.$items['keterangan'][$idx]:'' }}
                                            @endif
                                        </td>
                                        
                                        <td style=" border:solid; text-align: center; padding: 8px;">{{ $items['no_cont'][$idx] }}</td>
                                        <td style=" border:solid; text-align: center; padding: 8px;">{{ number_format($items['jumlah'][$idx], 0, ',', '.') }} {{ $items['satuan_jual'][$idx] }}</td>
                                        <td style=" border:solid; text-align: right; padding: 8px;">{{ number_format($items['harga_jual'][$idx], 0, ',', '.') }}</td>
                                        <td style=" border:solid; text-align: right; padding: 8px;">{{ number_format($items['harga_jual'][$idx] * $items['jumlah'][$idx], 0, ',', '.') }}</td>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
            @endfor
            <tr>
            <td colspan="6" style=" border:solid; text-align: right; padding: 10px;">
                DPP
                <br>
                @php
                    // Inisialisasi variabel untuk menentukan status PPN
                    $ppn_status = 'DIBEBASKAN'; // Default status PPN
                
                    // Loop melalui data untuk memeriksa status PPN
                    foreach ($data as $id_transaksi => $items) {
                        if (isset($items['invoice'])) {
                            foreach ($items['invoice'] as $idx => $invoice) {
                                // Jika salah satu item memiliki status PPN 'ya', set status PPN ke 'PPN 11%'
                                if ($items['status_ppn'][$idx] == 'ya') {
                                    $ppn_status = 'PPN 11%';
                                    break 2; // Keluar dari kedua loop jika status PPN ditemukan
                                }
                            }
                        }
                    }
                @endphp
                
                {{-- Menampilkan status PPN --}}
                @if($ppn_status == 'PPN 11%')
                    PPN 11%
                @else
                    PPN 11% (DIBEBASKAN)
                @endif
                
            </td>
            <td style=" border:solid; text-align: right; padding: 8px;">
{{-- Menampilkan total tanpa PPN --}}
{{ number_format($total, 0, ',', '.') }}
<br style=" border:solid; text-align: right;">

@php
    // Inisialisasi variabel untuk menentukan status PPN
    $total_ppn = 0; // Default nilai PPN

    // Loop melalui data untuk menghitung nilai PPN
    foreach ($data as $id_transaksi => $items) {
        if (isset($items['invoice'])) {
            foreach ($items['invoice'] as $idx => $invoice) {
                // Jika salah satu item memiliki status PPN 'ya', hitung nilai PPN
                if ($items['status_ppn'][$idx] == 'ya') {
                    $ppn_value = ($barang->value_ppn / 100) * $items['harga_jual'][$idx] * $items['jumlah'][$idx];
                    $total_ppn += $ppn_value; // Keluar dari kedua loop jika status PPN ditemukan
                }
            }
        }
    }
@endphp

{{-- Menampilkan nilai PPN --}}
@if($total_ppn > 0)
    {{ number_format($total_ppn, 0, ',', '.') }}
@else
    -
@endif

            </td>
        </tr>
        <tr>
            <td colspan="6" style=" border:solid; text-align: right; padding: 8px;">
                <b>TOTAL</b>
            </td>
            <td style=" border:solid; text-align: right; padding: 8px;">
                @php
                // Inisialisasi variabel untuk menentukan status PPN
                $has_ppn = false; // Status PPN, default tidak ada PPN
            
                // Loop melalui data untuk memeriksa status PPN
                foreach ($data as $id_transaksi => $items) {
                    if (isset($items['invoice'])) {
                        foreach ($items['invoice'] as $idx => $invoice) {
                            // Jika salah satu item memiliki status PPN 'ya', set $has_ppn menjadi true
                            if ($items['status_ppn'][$idx] == 'ya') {
                                $has_ppn = true;
                                break 2; // Keluar dari kedua loop jika status PPN ditemukan
                            }
                        }
                    }
                }
            
                // Hitung total PPN jika ada
                $total_with_ppn = $has_ppn ? ($total + $total_ppn) : $total; 
            @endphp
            
            {{-- Menampilkan total --}}
            <b style="text-align: right;">{{ number_format($total_with_ppn, 0, ',', '.') }}</b>
            
            </td>
        </tr>
        </tr>
    </tbody>
</table>
<h1 style="text-align: right; margin-right:50px; padding: 8px; font-size: 1rem"> Surabaya, {{ $tgl_inv1 }}</h1>
            <form action="{{ route('invoice-transaksi.store') }}" method="post" id="form">  
                @csrf
                @foreach ($data as $id_transaksi => $items)
                    @if (isset($items['invoice']))
                        @foreach ($items['invoice'] as $idx => $invoice)
                            <input type="hidden" name="tgl_invoice" value="{{ $tgl_inv2 }}">
                            <input type="hidden" name="tipe" value="{{ $tipe }}">
                            <input type="hidden" name="invoice" value="{{ $inv }}">
                            <input type="hidden" name="nsfp" value="{{  $modified  }}">
                            <input type="hidden" name="invoice_count" value="{{ $invoice_count }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][jumlah][]" value="{{ $items['jumlah'][$idx] ?? 0 }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][satuan_jual][]" value="{{ $items['satuan_jual'][$idx] ?? '' }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][harga_jual][]" value="{{ $items['harga_jual'][$idx] ?? 0 }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][jumlah_jual][]" value="{{ $items['jumlah_jual'][$idx] ?? 0 }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][keterangan][]" value="{{ $items['keterangan'][$idx] ?? '' }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][nama_satuan][]" value="{{ $items['nama_satuan'][$idx] ?? '' }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][nama_barang][]" value="{{ $items['nama_barang'][$idx] ?? '' }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][status_ppn][]" value="{{ $items['status_ppn'][$idx] ?? '' }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][value_ppn][]" value="{{ $items['value_ppn'][$idx] ?? 0 }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][value][]" value="{{ $items['value'][$idx] ?? 0 }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][satuan][]" value="{{ $items['satuan'][$idx] ?? '' }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][id_nsfp]" value="{{ $items['id_nsfp'] ?? '' }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][no]" value="{{ $items['no'] ?? '' }}">
                        @endforeach
                    @endif
                @endforeach
            
                <button class="btn bg-red-500 font-semibold justify-align-center text-white w-300 mt-3" type="button" onclick="window.history.back();">
                    Kembali
                </button>
                <button class="btn bg-green-500 font-semibold justify-align-center text-white w-300 mt-3" onclick="return confirm('Apakah anda yakin menyimpan Invoice?')" type="submit">
                    Submit Invoice
                </button>
            </form>
            
            </main>
    </x-keuangan.card-keuangan>

    <x-slot:script>
    </x-slot:script>
</x-Layout.layout>
