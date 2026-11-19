<x-Layout.layout>
    <div id="exp-jurnal"></div>
    <x-keuangan.card-keuangan>
        <style>
            .modal {
                top: 0;
                left: 0;
                width: 40%;
                z-index: 1000;
                background-color: white;
                padding: 20px;
                border-radius: 8px;
                max-width: 800px;
                position: relative;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                font-family: Arial, sans-serif;
            }

            .kembali-button {
                display: inline-block;
                padding: 12px 10px;
                background-color: #ad0f0f;
                color: white;
                text-decoration: none;
                border-radius: 8px;
                transition: background-color 0.3s;
            }

            .kembali-button:hover {
                background-color: #761408;
            }


            .close-button {
                position: absolute;
                top: 10px;
                right: 10px;
                font-size: 20px;
                background: none;
                border: none;
                color: #333;
                cursor: pointer;
            }

            /* Form labels and containers */
            .form-label {
                display: block;
                font-weight: bold;
                margin-top: 15px;
                margin-bottom: 5px;
            }

            .select-field {
                width: 90%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                margin-top: 5px;
                font-size: 14px;
            }

            .input-field {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                margin-top: 5px;
                font-size: 14px;
            }

            /* Submit button */
            .submit-button {
                display: block;
                width: 100%;
                padding: 12px;
                background-color: #28a745;
                color: white;
                border: none;
                border-radius: 5px;
                font-size: 16px;
                cursor: pointer;
                margin-top: 20px;
            }

            /* Label for extra info */
            .label-info {
                font-size: 12px;
                color: red;
            }

            /* Highlight baris yang dipilih */
            #coa_table tbody tr.highlight-row {
                background-color: #d4edda;
                /* Hijau muda */
                color: #155724;
                /* Hijau gelap untuk teks */
            }

            /* Pengaturan untuk tabel secara keseluruhan */
            table.dataTable {
                font-size: 13px;
                /* Ukuran font kecil */
                border-collapse: collapse;
                /* Menghilangkan ruang antar border */
                margin: 0;
                /* Menghapus margin tabel */

                /* Atur lebar tabel sesuai kontainer */
            }



            /* Kelas untuk tombol coklat */
            .btn-coklat {
                background-color: #6B4F1F;
                /* Coklat dasar */
                color: white;
                /* Warna teks putih */
                font-weight: bold;
                /* Teks tebal */
                padding: 10px 8px;
                /* Padding tombol */
                border-radius: 8px;
                /* Sudut tombol melengkung */
                border: 2px solid #6B4F1F;
                /* Border coklat */
                transition: background-color 0.3s ease, transform 0.3s ease;
                /* Efek transisi */
            }

            /* Efek hover pada tombol */
            .btn-coklat:hover {
                background-color: #8B5A2B;
                /* Coklat lebih gelap saat hover */
                transform: scale(1.05);
                /* Sedikit membesar saat hover */
            }


            /* Pengaturan untuk kolom (th dan td) */
            table.dataTable td,
            table.dataTable th {
                padding: 1px 2px;
                /* Padding minimal */
                border: 1px solid #ddd;
                /* Garis tepi */
                text-align: center;
            }

            .server-time {
                font-size: 18px;
                font-weight: bold;
                color: #ffffff;
                background-color: #007bff;
                padding: 10px 15px;
                border-radius: 5px;
                display: inline-block;
                box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            }

            /* Pengaturan untuk header tabel */
            table.dataTable thead th {
                background-color: #f4f4f4;
                text-align: center;
                font-weight: bold;
                position: relative;
            }

            /* Efek hover pada baris */
            table.dataTable tbody tr:hover {
                background-color: #f1faff;
                /* Efek hover */
            }

            /* Mencegah teks terpotong pada baris */
            table.dataTable tbody tr {
                /* Hindari teks terpotong */
            }

            /* Pengaturan untuk pagination dan search box */
            .dataTables_wrapper .dataTables_paginate {
                font-size: 10px;
            }

            .dataTables_wrapper .dataTables_filter input {
                height: 24px;
                font-size: 10px;
            }

            .dataTables_wrapper .dataTables_length select {
                height: 24px;
                font-size: 10px;
            }

            /* Menambahkan handle untuk resize kolom */
            th .resize-handle {
                position: absolute;
                right: 0;
                top: 0;
                height: 100%;
                cursor: ew-resize;
                /* Kursor untuk drag */
                background-color: rgba(0, 0, 0, 0.1);
            }
        </style>


        <x-slot:tittle>Summary Jurnal</x-slot:tittle>
        {{-- FILTER TANGGAL --}}
        <form action="{{ route('jurnal.summary-jurnal') }}" method="GET"
            class="flex flex-wrap items-end gap-4 mb-8 mt-8">

            <div>
                <label for="date" class="font-bold block mb-2">
                    Tanggal:
                </label>

                <input type="date" name="date" id="date" value="{{ $tanggal }}"
                    class="border-2 border-gray-300 rounded-lg px-3 py-2">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>

                <a href="{{ route('jurnal.summary-jurnal') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Reset
                </a>
            </div>
        </form>

        {{-- SUMMARY PER TIPE --}}
        <div class="space-y-8 mt-5">

            @forelse ($summary as $tipeJurnal => $items)

                @php
                    $subtotalDebit = $items->sum('total_debit');
                    $subtotalKredit = $items->sum('total_kredit');
                @endphp

                <div class="overflow-x-auto border rounded-lg">

                  <div class="bg-green-600 text-white font-bold px-4 py-3">
    SUMMARY JURNAL {{ $tipeJurnal }}
    {{ in_array($tipeJurnal, ['BBMAR', 'BBKAR']) ? '02' : '' }}
</div>

                    <table class="w-full border-collapse border border-gray-300 text-sm">

                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border p-2">No</th>
                                <th class="border p-2">No. Akun</th>
                                <th class="border p-2">Nama Akun</th>
                                <th class="border p-2">Total Debit</th>
                                <th class="border p-2">Total Kredit</th>
                                <th class="border p-2">Selisih</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($items as $index => $item)
                                <tr class="hover:bg-gray-50">

                                    <td class="border p-2 text-center">
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="border p-2 text-center">
                                        {{ $item->no_akun }}
                                    </td>

                                    <td class="border p-2 text-left">
                                        {{ $item->nama_akun }}
                                    </td>

                                    <td class="border p-2 text-right">
                                        {{ number_format((float) $item->total_debit, 2, ',', '.') }}
                                    </td>

                                    <td class="border p-2 text-right">
                                        {{ number_format((float) $item->total_kredit, 2, ',', '.') }}
                                    </td>

                                    <td class="border p-2 text-right">
                                        {{ number_format((float) $item->total_debit - (float) $item->total_kredit, 2, ',', '.') }}
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>

                        {{-- SUBTOTAL --}}
                        <tfoot class="bg-yellow-100 font-bold">
                            <tr>
                                <td colspan="3" class="border p-2 text-right">
                                    TOTAL {{ $tipeJurnal }}
                                </td>

                                <td class="border p-2 text-right">
                                    {{ number_format($subtotalDebit, 2, ',', '.') }}
                                </td>

                                <td class="border p-2 text-right">
                                    {{ number_format($subtotalKredit, 2, ',', '.') }}
                                </td>

                                <td class="border p-2 text-right">
                                    {{ number_format($subtotalDebit - $subtotalKredit, 2, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>

                    </table>
                </div>

            @empty

                <div class="border rounded-lg p-5 text-center text-gray-500">
                    <i class="fas fa-info-circle mr-1"></i>
                    Data jurnal tidak ditemukan pada tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}.
                </div>

            @endforelse
            {{-- GRAND TOTAL SEMUA TIPE --}}
@if ($summary->isNotEmpty())
    <div class="mt-8 bg-green-50 border-2 border-green-600 rounded-xl p-5 shadow-sm">

        <h3 class="text-lg font-bold text-green-800 mb-4">
            <i class="fas fa-calculator mr-2"></i>
            GRAND TOTAL SEMUA TIPE JURNAL
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- TOTAL DEBIT --}}
            <div class="bg-white border rounded-lg p-4">
                <div class="text-sm text-gray-500 font-semibold">
                    TOTAL DEBIT
                </div>

                <div class="text-xl font-bold text-green-700 mt-2">
                    Rp {{ number_format($grandDebit, 2, ',', '.') }}
                </div>
            </div>

            {{-- TOTAL KREDIT --}}
            <div class="bg-white border rounded-lg p-4">
                <div class="text-sm text-gray-500 font-semibold">
                    TOTAL KREDIT
                </div>

                <div class="text-xl font-bold text-blue-700 mt-2">
                    Rp {{ number_format($grandKredit, 2, ',', '.') }}
                </div>
            </div>

            {{-- SELISIH --}}
            <div class="bg-white border rounded-lg p-4">
                <div class="text-sm text-gray-500 font-semibold">
                    SELISIH
                </div>

                <div class="text-xl font-bold text-red-600 mt-2">
                    Rp {{ number_format($grandDebit - $grandKredit, 2, ',', '.') }}
                </div>
            </div>

        </div>
    </div>
@endif

        </div>

    </x-keuangan.card-keuangan>
</x-Layout.layout>

<script>
    function filterJurnal() {
        const date = document.getElementById('date').value;
        const tipe = document.getElementById('tipe').value;
        const params = new URLSearchParams();
        if (date) {
            params.append('date', date);
        }
        if (tipe) {
            params.append('tipe', tipe);
        }
        window.location.href = window.location.pathname + '?' + params.toString();
    }
</script>
