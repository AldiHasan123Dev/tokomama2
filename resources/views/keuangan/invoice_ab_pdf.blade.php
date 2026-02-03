<style>
    @page {
        size: A4 landscape;
        margin: 30mm;
    }

    body {
        font-family: "Courier New", monospace;
        background: #eee;
    }

    /* ===== HALAMAN ===== */
    .print-page {
        width: 297mm;
        min-height: 210mm;
        background: #fff;
        margin: auto;
        padding: 20px 24px;
        box-shadow: 0 0 10px rgba(0, 0, 0, .25);
    }

    /* ===== HEADER ===== */
    .invoice-title {
        text-align: center;
        font-size: 20px;
        /* 🔥 BESAR */
        font-weight: bold;
        margin-bottom: 14px;
        letter-spacing: 1px;
    }

    .invoice-info {
        font-size: 13px;
        /* 🔥 BESAR */
    }

    .invoice-info td {
        padding: 4px 6px;
    }

    /* ===== TABLE ===== */
    .invoice-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        /* 🔥 IDEAL */
    }

    .invoice-table th {
        border: 1px solid #000;
        padding: 6px;
        background: #f2f2f2;
    }

    .invoice-table td {
        border: 1px solid #000;
        padding: 6px;
    }

    /* ===== TOTAL ===== */
    .invoice-total td {
        font-weight: bold;
        font-size: 13px;
    }

    /* ===== PRINT MODE ===== */
    @media print {
        body {
            background: #fff;
        }

        .print-page {
            box-shadow: none;
            margin: 0;
            padding: 0;
        }
    }

    /* ===== TERBILANG ===== */
.terbilang-wrapper {
    margin-top: 14px;
    font-size: 12px;
}

.terbilang-label {
    width: 90px;
    font-weight: bold;
    vertical-align: top;
}

.terbilang-text {
    font-style: italic;
    line-height: 1.4;
}

/* ===== FOOTER ===== */
.footer {
    width: 100%;
    margin-top: 28px;
    font-size: 12px;
}

.footer td {
    vertical-align: top;
}

.footer .bank-info {
    line-height: 1.5;
}

.footer .sign {
    text-align: center;
    padding-top: 10px;
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

<body>
    <div class="print-page">
        {{-- HEADER --}}
        <div class="header">

            <div class="invoice-title">INVOICE ALAT BERAT</div>

            <table class="invoice-info">
                <tr>
                    <td width="140">No Invoice</td>
                    <td width="10">:</td>
                    <td><b>{{ $kodeInvoice }}</b></td>
                </tr>
                <tr>
                    <td>Telah terima dari</td>
                    <td>:</td>
                    <td>
                        <b>{{ $invoices->first()?->order?->customersAb?->nama ?? '-' }}</b>
                    </td>
                </tr>
                <tr>
                    <td>Untuk pembayaran</td>
                    <td>:</td>
                    <td>Jasa pemakaian Alat Berat dengan rincian sbb</td>
                </tr>
            </table>

        </div>

        {{-- CONTENT --}}
        <div class="content">

            <table class="invoice-table">
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="10%">Tgl Mulai</th>
                        <th width="10%">Tgl Selesai</th>
                        <th width="15%">Jenis Alat</th>
                        <th width="15%">Barang</th>
                        <th width="7%">Jam</th>
                        <th width="15%">Tarif Per Jam</th>
                        <th width="15%">Total</th>
                        <th width="10%">Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $grandTotal = 0;
                    @endphp

                    @foreach ($invoices as $invoice)
                        @php
                            $order = $invoice->order;
                        @endphp
                        @if (!$order)
                            @continue
                        @endif

                        {{-- HITUNG TOTAL INVOICE --}}
                        @php
                            $invoiceTotal = $invoice->total ?? 0;
                            $grandTotal += $invoiceTotal;
                        @endphp

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td style="text-align: center">{{ $order->tanggal_order }}</td>
                            <td style="text-align: center">{{ $invoice->sampai ?? '-' }}</td>
                            <td>{{ $order->tarif->alatBerat->nama_alat ?? '-' }}</td>
                            <td>{{ $order->barang }}</td>
                            <td style="text-align: right">{{ $invoice->total_jam ?? '-' }}</td>
                            <td style="text-align: right">Rp {{ number_format($order->tarif->tarif ?? 0, 0, ',', '.') }}</td>
                            <td style="text-align: right">Rp {{ number_format($invoiceTotal, 0, ',', '.') }}</td>
                            <td>{{ $order->keterangan ?? '-' }}</td>
                        </tr>

                        {{-- MOB --}}
                        @foreach ($order->mobs ?? [] as $mob)
                            @php
                                $grandTotal += $mob->nominal ?? 0;
                            @endphp

                            <tr>
                                <td></td>
                                <td colspan="5">
                                    🔹 Tambahan Tagihan #{{ $mob->id }} — {{ $mob->keterangan }}
                                </td>
                                <td></td>
                                <td style="text-align: right">
                                    Rp {{ number_format($mob->nominal ?? 0, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>

                <tfoot>
                    <tr class="invoice-total">
                        <td colspan="7" align="right">TOTAL</td>
                        <td style="text-align: right">
                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>

            </table>
        </div>
       <div class="terbilang-wrapper">
    <table width="100%">
        <tr>
            <td class="terbilang-label">Terbilang</td>
            <td width="10">:</td>
            <td class="terbilang-text">
                {{ ucwords(terbilang($grandTotal)) }} rupiah
            </td>
        </tr>
    </table>
</div>


        {{-- FOOTER --}}
       <table class="footer">
    <tr>
        <td width="60%" class="bank-info">
            <b>Pembayaran dapat dilakukan melalui :</b><br>
            Rekening No : 14000.4416.2999<br>
            Atas Nama : {{ $invoices->first()?->order?->customersAb?->nama ?? '-' }}<br>
            Bank : Bank Mandiri, Cab. Indrapura, Surabaya
        </td>

        <td width="40%" class="sign">
            Surabaya, 
            {{ \Carbon\Carbon::parse($invoices->first()?->tanggal_invoice)->format('d F Y') }}
            <br>
            Hormat Kami,<br><br><br><br>
            <b>Dwi Satria</b>
        </td>
    </tr>
</table>

    </div>

</body>
<script>
    window.onload = function() {
        window.print();
    };
</script>
