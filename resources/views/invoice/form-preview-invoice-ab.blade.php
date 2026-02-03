<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <style>
            .server-time {
                font-size: 18px;
                font-weight: bold;
                color: #fff04d;
                background-color: #ff0000;
                padding: 10px 15px;
                border-radius: 5px;
                display: inline-block;
                box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            }
        </style>
        <x-slot:tittle>Pengambilan Nomor Faktur Untuk Invoice Alat Berat</x-slot:tittle>
        <form action="{{ route('invoice-ab.preview') }}" method="post" id="form"  onsubmit="return validasiTanggalSelesai()">
            @csrf
            <p class="server-time">
                Silakan pilih Tgl Invoice, sebab Tgl dan Jam Server adalah : <span
                    id="server-time">{{ now()->format('Y-m-d H:i:s') }}</span>
            </p>
            <input type="date" name="tgl_invoice" value="{{ date('Y-m-d') }}">
            <input type="text" value="{{ $kode }}" name="kode" readonly>
             <input type="hidden" value="{{ $noInvoice }}" name="no" readonly>
            <input type="hidden" name="invoice_count" value="{{ $ordersCount }}" id="invoice_count">

            <div style="overflow-x: auto; margin-top: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
                <table style="border-collapse: collapse; width: 100%; margin: 0 auto;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">#</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Nama Alat</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Tgl Order</th>
                             <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Tgl Selesai</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Barang</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Total Jam</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Tarif / Jam</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Total Harga</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($orders as $item)
                            <input type="hidden" name="orders[{{ $loop->iteration }}][order_id]"
                                value="{{ $item['order_id'] }}">
                            <input type="hidden" name="orders[{{ $loop->iteration }}][tarif_id]"
                                value="{{ $item['tarif_id'] }}">
                            <input type="hidden" name="orders[{{ $loop->iteration }}][customer_id]"
                                value="{{ $item['customer_id'] }}">
                            <input type="hidden" id="total-input-{{ $loop->iteration }}"
                                name="orders[{{ $loop->iteration }}][total]" value="{{ $item['tarif'] }}">

                            <tr>
                                <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                    {{ $loop->iteration }}
                                </td>

                                <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                    {{ $item['nama_alat'] ?? '-' }}
                                </td>

                                <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                    {{ $item['tanggal_order'] }}
                                </td>

                                 <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                    <input type="date" name="orders[{{ $loop->iteration }}][tanggal_selesai]">
                                </td>

                                <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                    {{ $item['barang'] ?? '-' }}
                                </td>

                                <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                    <input type="number" min="1" value="1" class="jam-input"
                                        data-tarif="{{ $item['tarif'] }}" data-row="{{ $loop->iteration }}"
                                        style="width:70px; text-align:center;" oninput="hitungTotal(this)"
                                        name="orders[{{ $loop->iteration }}][jam]">
                                </td>


                                <td style="border: 1px solid #ddd; padding: 12px; text-align: right;">
                                    {{ number_format($item['tarif']) }}
                                </td>

                                <td style="border: 1px solid #ddd; padding: 12px; text-align: right;"
                                    id="total-harga-{{ $loop->iteration }}" name="">
                                    {{ number_format($item['tarif']) }}
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button class="btn bg-green-500 font-semibold justify-align-center text-white w-full mt-3"
                type="submit">Preview Invoice</button>
        </form>
    </x-keuangan.card-keuangan>

    <x-slot:script>
        <script>
            function hitungTotal(el) {
                let jam = parseInt(el.value) || 0;
                let tarif = parseInt(el.dataset.tarif) || 0;
                let row = el.dataset.row;

                let total = jam * tarif;

                // Update tampilan
                document.getElementById('total-harga-' + row).innerText =
                    total.toLocaleString('id-ID');

                // Update hidden input (INI YANG TERKIRIM KE SERVER)
                document.getElementById('total-input-' + row).value = total;
            }
            function validasiTanggalSelesai() {
        let valid = true;
        let pesan = '';

        document.querySelectorAll('input[name^="orders"][name$="[tanggal_selesai]"]').forEach(function (el, index) {
            if (!el.value) {
                valid = false;
                pesan = '❌ Tanggal selesai wajib diisi untuk semua order';
                el.focus();
            }
        });

        if (!valid) {
            alert(pesan);
            return false; // ⛔ stop submit
        }

        return true; // ✅ lanjut submit
    }
        </script>

    </x-slot:script>
</x-Layout.layout>
