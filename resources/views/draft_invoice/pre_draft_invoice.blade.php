<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Preview Draft Invoice</x-slot:tittle <div class="header">
        <div style=" display: flex; border: solid; justify-content: space-between; padding: 5px; margin-top: 30px;">
            <table style="width: 50%; border-collapse: collapse;" border="1">
                <thead>
                    <tr>
                        <td style="font-weight: bold; padding: 0; text-align: center;">
                            <img src="{{ asset('tokomama.svg') }}" alt="Logo"
                                style="height: 100px; margin-bottom: 1px; margin-left: 60px">
                            MAMA BAHAGIA <br>
                            Jl. Baru (Ruko depan PLN) Abepura, Jayapura <br>
                        </td>
                    </tr>
                </thead>
            </table>
            <div style="margin-right: 100px; width: 48%;">
                <table style="width: 120%; border: solid; border-collapse: collapse; margin-top: 10px;" border="1">
                    <thead>
                        <tr>
                            <td style="font-weight: bold;">Bill To :</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; margin:2px">{{ $transaksi->suratJalan->customer->nama }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">{{ $transaksi->suratJalan->customer->no_telp }}</td>
                        </tr>
                    </thead>
                </table>
                <table style="width: 120%; border: solid; border-collapse: collapse; margin-top: 10px;" border="1">
                    <thead>
                        <tr>
                            <td style="font-weight: bold;">{{ $transaksi->suratJalan->customer->alamat_npwp }}</td>
                        </tr>
                    </thead>
                </table>
            </div>
            <div style="margin-right: 50px; width: 108%; margin-top: 20px;">
                <table style="width: 100%; border: solid; border-collapse: collapse; margin-top: 10px;" border="1">
                    <thead>
                        <tr>
                            <td style="font-weight: bold; text-align: center">DRAFT INVOICE </td>
                        </tr>
                    </thead>
                </table>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <!-- Baris Pertama: NO INVOICE dan TGL INVOICE -->
                    <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                        <!-- Tabel NO INVOICE -->
                        <table style="width: 48%; border-collapse: collapse;" border="1">
                            <thead>
                                <tr>
                                    <th
                                        style="font-weight: bold; border: solid; padding: 8px; min-width: 150px; text-align: left; white-space: nowrap;">
                                        Tgl Draft Invoice : {{ $tgl_inv2 }}
                                    </th>
                                </tr>
                            </thead>
                        </table>

                        <!-- Tabel TGL INVOICE -->
                        <table style="width: 48%; border-collapse: collapse;" border="1">
                            <thead>
                                <tr>
                                    <th
                                        style="font-weight: bold; border: solid; padding: 8px; min-width: 150px; text-align: center; white-space: nowrap;">
                                        No Invoice : {{ $inv }}
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <!-- Baris Kedua: KETERANGAN dan TOTAL -->
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px">
                        <!-- Tabel KETERANGAN -->
                        <table style="width: 48%; border-collapse: collapse;" border="1">
                            <thead>
                                <tr>
                                    <th
                                        style="font-weight: bold; border: solid; padding: 8px; min-width: 150px; text-align: left; white-space: nowrap;">
                                        Terms : {{ $transaksi->suratJalan->customer->top }}
                                    </th>
                                </tr>
                            </thead>
                        </table>

                        <!-- Tabel TOTAL -->
                        <table style="width: 48%; border-collapse: collapse;" border="1">
                            <thead>
                                <tr>
                                    <th
                                        style="font-weight: bold; border: solid; padding: 8px; min-width: 150px; text-align: center; white-space: nowrap;">
                                        Tgl SJ : {{ $transaksi->suratJalan->created_at->format('Y-m-d') }}
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>

        </div>

        </div>
        {{-- <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <tbody>
                    <tr>
                        <td class="header-cell" style="font-size: 1rem; text-align:left">Customer :
                            {{ $transaksi->suratJalan->customer->nama . ' - ' . $transaksi->suratJalan->customer->kota ?? '-' }}</td>
                        <td class="header-cell" style="text-align: left; padding-left:420px; font-size: 1rem;">KAPAL
                            : {{ $transaksi->suratJalan->nama_kapal ?? '-' }}</td>

                    </tr>
                    <tr>
                        <td class="header-cell" style="text-align:left;font-size: 1rem;">PO :
                            {{ $transaksi->suratJalan->no_po ?? '-' }}</td>
                    </tr>
                </tbody>
            </table> --}}
        <main style="margin: 15px; margin-bottom: 30px;">

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

                <table
                    style="width: 100%; margin-top: 25px; margin: 5px; border: solid; border-collapse: collapse; margin-top: 5px;"
                    border="1">
                    @php
                        $end_date = min($start_date + $dates_per_page, $total_dates);
                    @endphp

                    <table style="width: 100%;  border: solid; border-collapse: collapse; margin-top: 5px;"
                        border="1">
                        <thead>
                            <tr>
                                <th style="text-align: center; border:solid; padding: 8px;">No.</th>

                                <th style="text-align: center;  border:solid; padding: 8px;">Nama Barang</th>
                                <th style="text-align: center;  border:solid; padding: 8px;">QTY</th>
                                <th style="text-align: center;  border:solid; padding: 8px;">Unit</th>
                                <th style="text-align: center;  border:solid; padding: 8px;">Harga Satuan(Rp)</th>
                                <th style="text-align: center;  border:solid; padding: 8px;">PO</th>
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
                                            <td style="border:solid; text-align: center; padding:2px;">
                                                {{ $index++ }}</td>
                                            <td style=" border:solid; padding-left: 10px;">
                                                {{ $items['nama_barang'][$idx] }}
                                                ({{ $items['jumlah_jual'][$idx] }}
                                                {{ $items['satuan_jual'][$idx] }})
                                                @if (str_contains($items['satuan_jual'][$idx], $items['nama_satuan'][$idx]))
                                                    @php
                                                        $t = $items['jumlah_jual'][$idx];
                                                        @endphp
                                                @else
                                                @php
                                                
                                                        $t =
                                                             $items['value'][$idx] *
                                                             $items['jumlah_jual'][$idx];
                                                    @endphp
                                                @endif


                                                @if ($items['satuan_jual'][$idx] != $items['nama_satuan'][$idx])
                                                    (Total {{ number_format($t) }} {{ $items['nama_satuan'][$idx] }}
                                                    {{ $items['keterangan'][$idx] != '' || !is_null($items['keterangan'][$idx]) ? '= ' . $items['keterangan'][$idx] : '' }})
                                                @else
                                                    {{ $items['keterangan'][$idx] != '' || !is_null($items['keterangan'][$idx]) ? '= ' . $items['keterangan'][$idx] : '' }}
                                                @endif
                                            </td>
                                            @php
                                             if ($items['status_ppn'][$idx] == 'ya') {
                                                 $harga_jual = $items['harga_jual'][$idx] * 1.11;
                                             } else {
                                                $harga_jual = $items['harga_jual'][$idx];
                                             }
                                        @endphp
                                            <td style=" border:solid; text-align: center; padding: 8px;">
                                                {{ $items['jumlah'][$idx] }}</td>
                                            <td style=" border:solid; text-align: center; padding: 8px;">
                                                {{ $items['satuan_jual'][$idx] }}</td>
                                            <td style=" border:solid; text-align: right; padding: 8px;">
                                                {{ number_format($harga_jual, 0, ',', '.') }}</td>
                                            <td style=" border:solid; text-align: center; padding: 8px;">
                                                {{ $items['no_po'][$idx] }}</td>
                                            <td style=" border:solid; text-align: right; padding: 8px;">
                                                {{ number_format($harga_jual * $items['jumlah'][$idx], 0, ',', '.') }}
                                            </td>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
            @endfor
            <tr>
                <td colspan="5">
                    <p style="font-weight: bold; padding-left: 30px; font-size: 0.8rem">
                        Terbilang:
                        @php
                            $dpp = $total;
                        @endphp
                        @php
                            // Inisialisasi variabel untuk menentukan status PPN
                            $total_ppn = 0; // Default nilai PPN

                            // Loop melalui data untuk menghitung nilai PPN
                            foreach ($data as $id_transaksi => $items) {
                                if (isset($items['invoice'])) {
                                    foreach ($items['invoice'] as $idx => $invoice) {
                                        // Jika salah satu item memiliki status PPN 'ya', hitung nilai PPN
                                        if ($items['status_ppn'][$idx] == 'ya') {
                                            $ppn_value = ($items['value_ppn'][$idx] / 100) * $dpp;
                                            $total_ppn += $ppn_value;
                                            // dd($total_ppn, $items['value_ppn'][$idx] / 100,$dpp); // Keluar dari kedua loop jika status PPN ditemukan
                                        }
                                    }
                                }
                            }
                        @endphp
                        @php
                            // Inisialisasi variabel untuk menentukan status PPN
                            $has_ppn = false; // Status PPN, default tidak ada PPN

                            // Loop melalui data untuk memeriksa status PPN
                            foreach ($data as $id_transaksi => $items) {
                                if (isset($items['invoice'])) {
                                    foreach ($items['invoice'] as $idx => $invoice) {
                                        // Jika salah satu item memiliki status PPN 'ya', set $has_ppn menjadi true
                                        if ($items['status_ppn'][$idx] == 'ya') {
                                            $total = $total * 1.11;
                                            $has_ppn = true;
                                            break 2; // Keluar dari kedua loop jika status PPN ditemukan
                                        }
                                    }
                                }
                            }

                            // Hitung total PPN jika ada
                            $total_with_ppn = $total;
                        @endphp
                        @if ($barang->status_ppn == 'ya')
                            {{ ucwords(strtolower(terbilang(round($total_with_ppn)))) }}
                            Rupiah
                        @else
                            {{ ucwords(strtolower(terbilang(round($total_with_ppn)))) }} Rupiah
                        @endif
                    </p>
                    <table style="font-size: 0.8rem;">
                        <tr>
                            <th style="text-align: left; padding-left: 50px; font-style: italic;">Pembayaran ke
                                rekening:</th>
                        </tr>
                        <tr>
                            <th style="text-align: left; padding-left: 50px; font-style: italic;">CV. Sarana Bahagia
                            </th>
                        </tr>
                        <tr>
                            <th style="text-align: left; padding-left: 50px; font-style: italic;"> Bank Mandiri
                                : 14.000.45006.005</th>
                            <th></th>
                        </tr>
                        <tr>
                            <th style="text-align: left; padding-left: 50px;"></th>
                        </tr>
                    </table>
                </td>
                {{-- <td colspan="1" style=" border:solid; text-align: right; padding: 10px;">
                    Subtotal
                    <br>
                    DPP 11/12
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

                   
                    @if ($ppn_status == 'PPN 11%')
                        PPN 12%
                    @else
                        PPN 12% (DIBEBASKAN)
                    @endif

                </td> --}}
                {{-- <td style=" border:solid; text-align: right; padding: 8px;">
                    {{ number_format($total, 0, ',', '.') }}
                    <br style=" border:solid; text-align: right;">
                    @php
                        $dpp = (11 / 12) * $total;
                    @endphp
                    @if($items['status_ppn'][$idx] == 'ya')
                    {{ number_format($dpp, 0, ',', '.') }}
                @else
                    -
                @endif
                    <br style=" border:solid; text-align: right;">

                    @php
                      
                        $total_ppn = 0;

                        
                        foreach ($data as $id_transaksi => $items) {
                            if (isset($items['invoice'])) {
                                foreach ($items['invoice'] as $idx => $invoice) {
                                    
                                    if ($items['status_ppn'][$idx] == 'ya') {
                                        $ppn_value = ($items['value_ppn'][$idx] / 100) * $dpp;
                                        $total_ppn += $ppn_value; 
                                    }
                                }
                            }
                        }
                    @endphp

                   
                    @if ($total_ppn > 0)
                        {{ number_format($total_ppn, 0, ',', '.') }}
                    @else
                        -
                    @endif

                </td>
            </tr> --}}
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
                        $total_with_ppn = $total;
                    @endphp

                    {{-- Menampilkan total --}}
                    <b style="text-align: right;">{{ number_format($total_with_ppn, 0, ',', '.') }}</b>

                </td>
            </tr>
            </tr>
            </tbody>
            </table>
            <div class="footer">
                <table style="font-size: 0.8rem; margin-left: 870px; margin-top: 20px">
                    <tr>
                        <th style="text-align: left; padding-left: 50px; font-style: italic;"></th>
                        <td style="align-items:right ;text-align: center;">Jayapura, {{ $tgl_inv1 }}</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding-left: 50px; font-style: italic;"></th>
                        <td style="text-align: center;">Hormat Kami</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding-left: 50px; font-style: italic;"></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding-left: 50px;"></th>
                        <th style="padding-top:30px">(MAMA BAHAGIA)</th>
                    </tr>
                </table>

            </div>
            <form action="{{ route('pending.invoice.draft_invoice.cetak') }}" method="post" target="_blank" id="form">
                @csrf
                @foreach ($data as $id_transaksi => $items)
                    @if (isset($items['invoice']))
                        @foreach ($items['invoice'] as $idx => $invoice)
                            <input type="hidden" name="tgl_invoice" value="{{ $tgl_inv2 }}">
                            <input type="hidden" name="tipe" value="{{ $tipe }}">
                            <input type="hidden" name="invoice" value="{{ $inv }}">
                            <input type="hidden" name="nsfp" value="{{ $modified }}">
                            <input type="hidden" name="invoice_count" value="{{ $invoice_count }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][jumlah][]"
                                value="{{ $items['jumlah'][$idx] ?? 0 }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][satuan_jual][]"
                                value="{{ $items['satuan_jual'][$idx] ?? '' }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][harga_jual][]"
                                value="{{ $items['harga_jual'][$idx] ?? 0 }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][id_surat_jalan][]"
                                value="{{ $items['id_surat_jalan'][$idx] ?? 0 }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][jumlah_jual][]"
                                value="{{ $items['jumlah_jual'][$idx] ?? 0 }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][keterangan][]"
                                value="{{ $items['keterangan'][$idx] ?? '' }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][nama_satuan][]"
                                value="{{ $items['nama_satuan'][$idx] ?? '' }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][nama_barang][]"
                                value="{{ $items['nama_barang'][$idx] ?? '' }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][status_ppn][]"
                                value="{{ $items['status_ppn'][$idx] ?? '' }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][value_ppn][]"
                                value="{{ $items['value_ppn'][$idx] ?? 0 }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][value][]"
                                value="{{ $items['value'][$idx] ?? 0 }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][satuan][]"
                                value="{{ $items['satuan'][$idx] ?? '' }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][id_nsfp]"
                                value="{{ $items['id_nsfp'] ?? '' }}">
                            <input type="hidden" name="data[{{ $id_transaksi }}][no]"
                                value="{{ $items['no'] ?? '' }}">
                        @endforeach
                    @endif
                @endforeach

                <button class="btn bg-red-500 font-semibold justify-align-center text-white w-300 mt-3" type="button"
                    onclick="window.history.back();">
                    Kembali
                </button>
                <button class="btn bg-green-500 font-semibold justify-align-center text-white w-300 mt-3"type="submit">
                    Cetak Draft Invoice
                </button>
            </form>

        </main>
        </div>
    </x-keuangan.card-keuangan>

    <x-slot:script>
    </x-slot:script>
</x-Layout.layout>
