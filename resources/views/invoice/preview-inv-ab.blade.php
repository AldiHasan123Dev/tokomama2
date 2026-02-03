<x-Layout.layout>
    <style>
        /* ================= INVOICE BASE ================= */
        .invoice {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            width: 100%;
        }

        /* ================= TITLE ================= */
        .invoice-title {
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 12px;
            letter-spacing: 1px;
        }

        /* ================= HEADER INFO ================= */
        .invoice-info {
            margin-bottom: 10px;
        }

        .invoice-info td {
            padding: 2px 4px;
            vertical-align: top;
        }

        /* ================= TABLE ================= */
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .invoice-table th,
        .invoice-table td {
            border: 1px solid #000;
            padding: 4px 5px;
            font-size: 12px;
        }

        .invoice-table th {
            text-align: center;
            font-weight: bold;
        }

        .invoice-table td {
            vertical-align: middle;
        }

        /* ================= ALIGN ================= */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* ================= TOTAL ================= */
        .invoice-total td {
            font-weight: bold;
        }

        /* ================= TERBILANG ================= */
        .terbilang {
            font-style: italic;
            font-weight: bold;
            padding-top: 5px;
        }

        /* ================= FOOTER ================= */
        .footer {
            margin-top: 18px;
            width: 100%;
        }

        .footer td {
            padding: 3px 4px;
            vertical-align: top;
        }

        /* ================= PRINT ================= */
        @media print {
            body {
                margin: 0;
            }
        }
    </style>

    @php
        if (!function_exists('terbilang')) {
            function terbilang($angka)
            {
                $angka = abs($angka);
                $huruf = [
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
                    return $huruf[$angka];
                }
                if ($angka < 20) {
                    return terbilang($angka - 10) . ' belas';
                }
                if ($angka < 100) {
                    return terbilang(intval($angka / 10)) . ' puluh ' . terbilang($angka % 10);
                }
                if ($angka < 200) {
                    return 'seratus ' . terbilang($angka - 100);
                }
                if ($angka < 1000) {
                    return terbilang(intval($angka / 100)) . ' ratus ' . terbilang($angka % 100);
                }
                if ($angka < 2000) {
                    return 'seribu ' . terbilang($angka - 1000);
                }
                if ($angka < 1000000) {
                    return terbilang(intval($angka / 1000)) . ' ribu ' . terbilang($angka % 1000);
                }
                if ($angka < 1000000000) {
                    return terbilang(intval($angka / 1000000)) . ' juta ' . terbilang($angka % 1000000);
                }
                return 'angka terlalu besar';
            }
        }
    @endphp

<form action="{{ route('simpan-invoice-ab.store') }}" method="post">
    @csrf
    <div class="invoice">

        {{-- JUDUL --}}
        <div class="invoice-title">INVOICE</div>

        {{-- HEADER --}}
        <table class="invoice-info">
            <tr>
                <td width="140">No Invoice</td>
                <td width="10">:</td>
                <td><b>{{ $KodeInvoice }}</b></td>
            </tr>
            <tr>
                <td>Telah terima dari</td>
                <td>:</td>
                <td><b>{{ $customerNama }}</b></td>
            </tr>
            <tr>
                <td>Untuk pembayaran</td>
                <td>:</td>
                <td>Jasa pemakaian Alat Berat dengan rincian sbb</td>
            </tr>
        </table>

        {{-- TABEL --}}
        <table class="invoice-table">
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th colspan="2">Tgl Pemakaian</th>
                    <th rowspan="2">Jenis Alat</th>
                    <th rowspan="2">Barang</th>
                    <th rowspan="2">Total Jam</th>
                    <th rowspan="2">Tarif / Jam</th>
                    <th rowspan="2">Total</th>
                    <th rowspan="2">Ket</th>
                </tr>
                <tr>
                    <th>Mulai</th>
                    <th>Selesai</th>
                </tr>
            </thead>
            <tbody>

                @php
                    $no = 1;
                    $grandTotal = 0;
                @endphp

                @foreach ($dataOrders as $order)
                    <tr>
                        @php
                            $tarif = $tarifs[$order['tarif_id']] ?? 0;
                        @endphp
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="text-center">{{ $order['tanggal_order'] }}</td>
                        <td class="text-center">{{ $order['tanggal_selesai'] }}</td>
                        <td>{{ $order['jenis_alat'] }}</td>
                        <td>{{ $order['barang'] }}</td>
                        <td class="text-center">{{ $order['jam'] }}</td>
                        <td class="text-right"> Rp {{ number_format($tarif, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($order['total'], 0, ',', '.') }}</td>
                        <td>{{ $order['ket'] ?? '-' }}</td>
                    </tr>
                    {{-- DETAIL MOB --}}
                    @if (!empty($order['id_mob']))
                        @foreach ($order['id_mob'] as $mob)
                            <tr>
                                <td style="border-top:0;"></td>
                                <td style="border-top:0;"></td>
                                <td style="border-top:0;"></td>

                                <td colspan="4" style="border-top:0; padding-left:25px; font-weight:bold;">
                                    🔹 Tambahan Tagihan #{{ $mob['id'] }}
                                    <span style="font-weight:normal; font-style:italic;">
                                        — {{ $mob['keterangan'] }}
                                    </span>
                                </td>

                                <td class="text-right" style="border-top:0; font-weight:bold;">
                                    Rp {{ number_format($mob['nominal'], 0, ',', '.') }}
                                </td>

                                <td style="border-top:0;"></td>
                            </tr>
                        @endforeach
                    @endif

                    @php
                        $grandTotal += $order['total'];

                        // Tambahkan MOB jika ada
                        if (!empty($order['id_mob'])) {
                            foreach ($order['id_mob'] as $mob) {
                                $grandTotal += (float) ($mob['nominal'] ?? 0);
                            }
                        }
                    @endphp
                @endforeach

                <tr class="invoice-total">
                    <td colspan="7" class="text-right">Total</td>
                    <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        {{-- TERBILANG --}}
        <table class="footer">
            <tr>
                <td width="90"><b>Terbilang :</b></td>
                <td class="terbilang">{{ ucwords(terbilang($grandTotal)) }} rupiah</td>
            </tr>
        </table>

        {{-- FOOTER --}}
        <table class="footer">
            <tr>
                <td width="60%">
                    <b>Pembayaran dapat dilakukan melalui :</b><br>
                    Rekening No : 14000.4416.2999<br>
                    Atas Nama : {{ $customerNama }}<br>
                    Bank : Bank Mandiri, Cab. Indrapura, Surabaya
                </td>
                <td class="text-center">
                    Surabaya, {{ \Carbon\Carbon::parse($tgl_invoice)->format('d F Y') }}<br>
                    Hormat Kami,<br><br><br>
                    <b>Dwi Satria</b>
                </td>
            </tr>
        </table>
        @foreach ($dataOrders as $index => $order)

    {{-- DATA ORDER --}}
    <input type="hidden" name="orders[{{ $index }}][order_id]" value="{{ $order['order_id'] }}">
    <input type="hidden" name="orders[{{ $index }}][tgl_invoice]" value="{{ $tgl_invoice }}">
     <input type="hidden" name="orders[{{ $index }}][no]" value="{{ $noInvoice }}">
    <input type="hidden" name="orders[{{ $index }}][kode_invoice]" value="{{ $KodeInvoice }}">
    <input type="hidden" name="orders[{{ $index }}][penerima]" value="{{ $order['customer_id'] }}">
    <input type="hidden" name="orders[{{ $index }}][sampai]" value="{{ $order['tanggal_selesai'] }}">
    <input type="hidden" name="orders[{{ $index }}][total_jam]" value="{{ $order['jam'] }}">
    <input type="hidden" name="orders[{{ $index }}][total]" value="{{ $order['total'] }}">
@endforeach

        <div class="flex gap-3 mt-4">
            {{-- Tombol Kembali --}}
            <a href="{{ url()->previous() }}"
                class="w-1/2 text-center bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 rounded">
                ← Kembali
            </a>

            {{-- Tombol Simpan --}}
            <button type="submit" name="action" value="simpan"
                class="w-1/2 bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded">
                💾 Simpan
            </button>
        </div>

    </div>
    </form>
</x-Layout.layout>
