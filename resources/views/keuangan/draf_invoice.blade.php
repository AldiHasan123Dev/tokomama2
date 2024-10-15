<x-Layout.layout>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        .logo {
            max-width: 100%;
            height: 100px;
        }

        body {
            width: 100%;
            /* padding: 10px 30px; */
        }

        table.table {
            margin-top: 10px;
        }

        .border.border-black {
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
    </style>

    @if(session('error'))
    <div role="alert" class="alert alert-error mb-5">
        <i class="fa-regular fa-circle-xmark"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif
    <x-keuangan.card-keuangan>
        <x-slot:tittle>DRAF INVOICE</x-slot:tittle>
        <x-slot:button>
            <form action="{{ route('keuangan.invoice.submit',$surat_jalan) }}" method="post">
                @csrf
                <input type="hidden" name="total" id="total">
                <button type="submit" onclick="return confirm('Submit Invoice?')"
                    class="btn btn-primary btn-sm text-black">Submit Invoice</button>
            </form>
        </x-slot:button>
        <div class="overflow-x-auto">
            <main>
                <table>
                    <thead>
                        <tr>
                            <th rowspan="4" style="width: 20%">
                                <img src="{{ url('logo_sb.svg') }}" class="logo mx-auto">
                            </th>
                            <td style="font-weight: bold; font-size: 1.3rem;">CV. SARANA BAHAGIA</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>Jl. Kalianak 55 Blok G, Surabaya</td>
                            <td style="font-weight: bold; font-size: 1.5rem; text-align: center;"><u>INVOICE</u></td>
                        </tr>
                        <tr>
                            <td>Telp: 031-7495507</td>
                            <td style="text-align: center;">NO: {{ $surat_jalan->invoice ?? '-' }}</td>
                        </tr>
                        <br>
                        <tr>
                            <td style="text-align: left; padding-left: 45px;" colspan="2">Customer &nbsp;&nbsp;&nbsp; :
                                &nbsp;&nbsp;&nbsp;
                                {{$surat_jalan->customer->nama ?? '-' }}</td>
                            <td style="text-align: center;"><span style="font-weight: bold;">KAPAL: </span>
                                &nbsp;&nbsp;&nbsp;
                                {{ $surat_jalan->nama_kapal }}
                            </td>
                        </tr>
                    </thead>
                </table>

                <table class="table border border-black">
                    <thead>
                        <tr>
                            <th class="border border-black">No.</th>
                            <th class="border border-black">Tgl Barang Masuk</th>
                            <th class="border border-black">Nama Barang</th>
                            <th class="border border-black">No. Cont</th>
                            <th class="border border-black">Quantity</th>
                            <th class="border border-black">Harga Satuan</th>
                            <th class="border border-black">Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $total = 0;
                        function terbilang($angka) {
                            $angka = (float)$angka;
                            $bilangan = array(
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
                                    'sebelas'
                                );
                                if ($angka < 12) {
                                    return $bilangan[$angka];
                                } else if ($angka < 20) {
                                    return $bilangan[$angka - 10] . ' belas';
                                } else if ($angka < 100) {
                                    $hasil_bagi = (int)($angka / 10);
                                    $hasil_mod = $angka % 10;
                                    return trim(sprintf('%s puluh %s', $bilangan[$hasil_bagi], $bilangan[$hasil_mod]));
                                } else if ($angka < 200) {
                                    return 'seratus ' . terbilang($angka - 100);
                                } else if ($angka < 1000) {
                                    $hasil_bagi = (int)($angka / 100);
                                    $hasil_mod = $angka % 100;
                                    return trim(sprintf('%s ratus %s', $bilangan[$hasil_bagi], terbilang($hasil_mod)));
                                } else if ($angka < 2000) {
                                    return 'seribu ' . terbilang($angka - 1000);
                                } else if ($angka < 1000000) {
                                    $hasil_bagi = (int)($angka / 1000);
                                    $hasil_mod = $angka % 1000;
                                    return trim(sprintf('%s ribu %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
                                } else if ($angka < 1000000000) {
                                    $hasil_bagi = (int)($angka / 1000000);
                                    $hasil_mod = $angka % 1000000;
                                    return trim(sprintf('%s juta %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
                                } else if ($angka < 1000000000000) {
                                    $hasil_bagi = (int)($angka / 1000000000);
                                    $hasil_mod = fmod($angka, 1000000000);
                                    return trim(sprintf('%s miliar %s', terbilang($hasil_bagi), terbilang($hasil_mod)));
                                } else {
                                    return 'Angka terlalu besar';
                                }
                            }
                        @endphp
                        @foreach ($surat_jalan->transactions as $item)
                        <tr>
                            <td class="text-center border border-black">{{ $loop->iteration }}</td>
                            <td class="text-center border border-black">{{ date('d M Y',
                                strtotime($surat_jalan->tgl_sj)) }}</td>
                            <td class="text-center border border-black">
                                {{ $item->barang->nama }} <br> 
                                {{-- @if (str_contains($item->barang->nama, '@')) --}}
                                @if($item->satuan_jual != $item->satuan_beli)
                                    (Total {{ number_format($item->jumlah_beli * $item->barang->value) }} Kg)
                                @else
                                    (Total {{ number_format($item->jumlah_beli) }} {{$item->satuan_beli}})
                                @endif
                            </td>
                            <td class="text-center border border-black">{{ $surat_jalan->no_cont }}</td>
                            <td class="text-center border border-black">{{ $item->jumlah_jual }} {{ $item->satuan_jual }}</td>
                            <td class="text-center border border-black">{{ number_format($item->harga_jual) }} / {{ $item->satuan_jual }}</td>
                            <td class="text-center border border-black">{{ number_format($item->jumlah_jual *
                                $item->harga_jual) }}</td>
                        </tr>
                        @php
                        $total += ($item->jumlah_jual * $item->harga_jual);
                        @endphp
                        @endforeach
                        <tr>
                            <td class="text-center border border-black"></td>
                            <td class="text-center border border-black"></td>
                            <td class="text-center border border-black"></td>
                            <td class="text-center border border-black"></td>
                            <td class="text-center border border-black"></td>
                            <td class="border border-black">
                                DPP
                                <br>
                                PPN 11% (DIBEBASKAN)
                            </td>
                            <td class="border border-black">
                                {{ number_format($total) }}
                                <br>
                                
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center border border-black"></td>
                            <td class="text-center border border-black"></td>
                            <td class="text-center border border-black"></td>
                            <td class="text-center border border-black"></td>
                            <td class="text-center border border-black"></td>
                            <td class="border border-black">
                                <b>TOTAL</b>
                            </td>
                            <td class="border border-black">
                                <b>{{ number_format($total) }}</b>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p style="font-weight: bold;">TERBILANG: {{ strtoupper(terbilang($total)) }} RUPIAH </p>

                <br>

                <table>
                    <tr>
                        <th style="text-align: left; padding-left: 50px;">Pembayaran ke rekening:</th>
                        <td style="text-align: center;">Surabaya, @for($i = 1; $i <= 1; $i++){{ date('d M Y',
                                strtotime($surat_jalan->tgl_sj)) }}@endfor</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding-left: 50px;">CV. Sarana Bahagia</th>
                        <td style="text-align: center;">Hormat Kami</td>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding-left: 50px;">Mandiri (Cab.Indrapura) : 14.000.45006.005
                        </th>
                        <th></th>
                    </tr>
                    <tr>
                        <th style="text-align: left; padding-left: 50px;"></th>
                        <th>(Dwi Satria Wardana)</th>
                    </tr>
                </table>
            </main>
        </div>
    </x-keuangan.card-keuangan>
    <script>
        $('#total').val(@json($total));
    </script>
</x-Layout.layout>