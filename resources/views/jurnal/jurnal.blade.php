<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <style>
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

            #coa_table th,
            #coa_table td {
                width: 50px;
                /* Set lebar untuk semua kolom */
            }

            #coa_table th:nth-child(1),
            #coa_table td:nth-child(1) {
                width: 2px;
                /* Set lebar untuk kolom pertama */
                margin: 0;
            }

            #coa_table th:nth-child(2),
            #coa_table td:nth-child(2) {
                width: 3px;
                /* Set lebar untuk kolom kedua */
            }

            #coa_table th:nth-child(3),
            #coa_table td:nth-child(3) {
                width: 30px;
                /* Set lebar untuk kolom kedua */
            }

            #coa_table th:nth-child(4),
            #coa_table td:nth-child(4) {
                width: 30px;
                /* Set lebar untuk kolom kedua */
            }

            #coa_table th:nth-child(5),
            #coa_table td:nth-child(5) {
                width: 50px;
                /* Set lebar untuk kolom pertama */
                margin: 0;
            }

            #coa_table th:nth-child(6),
            #coa_table td:nth-child(6) {
                width: 3px;
                /* Set lebar untuk kolom kedua */
            }

            #coa_table th:nth-child(7),
            #coa_table td:nth-child(7) {
                width: 30px;
                /* Set lebar untuk kolom kedua */
            }

            #coa_table th:nth-child(8),
            #coa_table td:nth-child(8) {
                width: 30px;
                /* Set lebar untuk kolom kedua */
            }

            #coa_table th:nth-child(9),
            #coa_table td:nth-child(9) {
                width: 50px;
                /* Set lebar untuk kolom pertama */
                margin: 0;
            }

            #coa_table th:nth-child(10),
            #coa_table td:nth-child(10) {
                width: 3px;
                /* Set lebar untuk kolom kedua */
            }

            #coa_table th:nth-child(11),
            #coa_table td:nth-child(11) {
                width: 30px;
                /* Set lebar untuk kolom kedua */
            }

            #coa_table th:nth-child(12),
            #coa_table td:nth-child(12) {
                width: 30px;
                /* Set lebar untuk kolom kedua */
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
                width: 5px;
                height: 100%;
                cursor: ew-resize;
                /* Kursor untuk drag */
                background-color: rgba(0, 0, 0, 0.1);
            }
        </style>


        <x-slot:tittle>Menu Jurnal</x-slot:tittle>
        <div class="overflow-x-auto mb-8">
            <a href="{{ route('jurnal-manual.index') }}">
                <button class="btn bg-green-500 text-white font-bold hover:bg-green-700 m-2x">Input Jurnal</button>
            </a>

            <a href="{{ route('jurnal.jurnal-merger') }}">
                <button class="btn bg-gray-500 text-white font-bold hover:bg-gray-700 mb-2px">Merge Jurnal</button>
            </a>

            <div class="flex flex-row mb-16 mt-8">
                <label for="month" class="font-bold mt-2">Bulan:</label>
                @for ($i = 1; $i <= 12; $i++)
                    @php
                        $monthName = date('M', mktime(0, 0, 0, $i, 1));
                    @endphp
                    <form action="" method="GET">
                        <input type="hidden" name="month" value="{{ $i }}">
                        <input type="hidden" name="year" value="{{ date('Y') }}">
                        <button
                            class="px-3 py-2 border-2 border-green-300 hover:bg-green-300 hover:text-white duration-300 rounded-xl mx-1 
                                {{ request('month') == $i ? 'bg-green-300 text-white' : '' }}">
                            {{ $monthName }}
                        </button>
                    </form>
                @endfor

                <div class="w-full ml-10 mt-3">
                    <b>Tahun : </b>
                    <select class="js-example-basic-single w-1/2" name="akun" id="thn">
                        <option selected value="{{ date('Y') }}">{{ date('Y') }}</option>
                        @for ($year = date('Y'); $year >= 2024; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="mb-10 flex justify-between">
                {{-- <div>
                    Filter Tanggal : <input type="date" name="tanggal" id="tanggal">
                </div> --}}
                <div>
                    <div class="flex flex-row mr-10">
                        <label for="month" class="font-bold mt-2">Tipe :</label>
                        @php
                            $types = ['JNL', 'BKK', 'BKM', 'BBK', 'BBM']; // Daftar tipe
                        @endphp

                        @foreach ($types as $type)
                            <form action="" method="GET" class="inline-block">
                                <input type="hidden" name="tipe" value="{{ $type }}">
                                <input type="hidden" name="month" value="{{ request('month') }}">
                                <input type="hidden" name="year" value="{{ date('Y') }}">
                                <button type="submit"
                                    class="px-3 py-2 border-2 border-green-300 hover:bg-green-300 hover:text-white duration-300 rounded-xl mx-1
                                        {{ request('tipe') == $type ? 'bg-green-300 text-white' : '' }}">
                                    {{ $type }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
                <div>
                    <form action="{{ route('jurnal.edit') }}" method="get" class="ml-10">
                        <input type="hidden" name="nomor" id="nomor">
                        <button class="btn bg-yellow-500 text-white font-bold hover:bg-yellow-700" id="edit">Edit
                            Jurnal</button>
                    </form>
                </div>
            </div>


            <div class="dataTable-container">
                <table id="coa_table" class="dataTable cell-border hover display nowrap compact">
                    <!-- head -->
                    <thead>
                        <tr>
                            <th class="text-center">Tanggal <div class="resize-handle"></div>
                            </th>
                            <th class="text-center">Tipe <div class="resize-handle"></div>
                            </th>
                            <th class="text-center">Nomor <div class="resize-handle"></div>
                            </th>
                            <th class="text-center">No. Akun <div class="resize-handle"></div>
                            </th>
                            <th class="text-center">Nama Akun <div class="resize-handle"></div>
                            </th>
                            <th class="text-center">Invoice <div class="resize-handle"></div>
                            </th>
                            <th class="text-center">Debit <div class="resize-handle"></div>
                            </th>
                            <th class="text-center">Kredit <div class="resize-handle"></div>
                            </th>
                            <th class="text-center">Keterangan <div class="resize-handle"></div>
                            </th>
                            <th class="text-center">Invoice Supplier <div class="resize-handle"></div>
                            </th>
                            <th class="text-center">Nopol <div class="resize-handle"></div>
                            </th>
                            <th class="text-center">Kaitan BB Pembantu <div class="resize-handle"></div>
                            </th>
                            <th class="hidden">no</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($data as $d)
                            <tr>
                                <td class="text-start">{{ $d->tgl }}</td>
                                <td class="text-start">{{ $d->tipe }}</td>
                                <td class="text-start">{{ $d->nomor }}</td>
                                <td class="text-start">{{ $d->no_akun }}</td>
                                <td class="text-start">{{ $d->nama_akun }}</td>
                                <td class="text-start"> {{ $d->invoice == 0 ? '' : $d->invoice ?? '-' }}</td>
                                <td class="text-end">{{ number_format($d->debit, 2, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($d->kredit, 2, ',', '.') }}</td>
                                <td class="text-start">{{ $d->keterangan }}</td>
                                <td>
                                    {{ $d->invoice_external == 0 ? '' : $d->invoice_external ?? '-' }}
                                </td>
                                <td>{{ $d->nopol }}</td>
                                <td>{{ $d->keterangan_buku_besar_pembantu }}</td>
                                <td class="hidden">{{ $d->no }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </x-keuangan.card-keuangan>

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Monitoring jurnal</x-slot:tittle>
        <table id="monitoring_JNL" class="cell-border hover display nowrap compact">
            <thead>
                <th>Total Debet</th>
                <th>Total Kredit</th>
                <th>Nomor JNL Terakhir</th>
            </thead>
            <tbody>
                <tr>
                    <td class="text-end">{{ number_format($MonJNL->sum('debit'), 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($MonJNL->sum('kredit'), 2, ',', '.') }}</td>
                    <td>{{ $LastJNL->max('no') }}</td>
                </tr>
            </tbody>
        </table>
        <center>
            <div class="overflow-x-auto">
                <h1 class="font-bold text-xl">Cek Jurnal Tidak Balance</h1>
                <table border="1" class="table">
                    <thead>
                        <tr>
                            <th width="30%">
                                <div align="center">#</div>
                            </th>

                            <th width="70%">
                                <div align="left">Nomor Jurnal</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($notBalance))
                            <tr>
                                <td colspan="2">
                                    <div align="center">Semua Jurnal Balance</div>
                                </td>
                            </tr>
                        @else
                            @foreach ($notBalance as $nb)
                                <tr class="hover">
                                    <td>
                                        <div align="center">{{ $loop->iteration }}</div>
                                    </td>

                                    <td>
                                        <div align="left">{{ $nb }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif


                    </tbody>
                </table>
            </div>
        </center>
    </x-keuangan.card-keuangan>

    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/select/2.0.3/js/dataTables.select.js"></script>
    <script src="https://cdn.datatables.net/2.1.0/js/dataTables.tailwindcss.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#coa_table').DataTable({
                pageLength: 20, // Batas data per halaman
                lengthMenu: [20, 50, 100, 150], // Pilihan jumlah data per halaman
                ordering: false,
            });
            var MonJNL = $('#monitoring_JNL').DataTable({})

            $('#coa_table tbody').on('click', 'tr', function() {
                const row = table.row(this).data();

                // Hapus latar belakang hijau dari semua baris sebelumnya
                $('#coa_table tbody tr').removeClass('highlight-row');

                // Tambahkan latar belakang hijau ke baris yang diklik
                $(this).addClass('highlight-row');

                console.log(row);
                $('#nomor').val($.trim(row[2]).replace(/%2F/g, ''));
                $('.btn').removeClass('hidden');
                $('#print').attr('href', "{{ route('invoice.print', ['id' => ':id']) }}".replace(':id', row
                    .id));
            });

            $(`#thn`).on(`change`, function() {
                $(`#y1`).val($(this).val())
                $(`#y2`).val($(this).val())
                $(`#y3`).val($(this).val())
                $(`#y4`).val($(this).val())
                $(`#y5`).val($(this).val())
                $(`#y6`).val($(this).val())
                $(`#y7`).val($(this).val())
                $(`#y8`).val($(this).val())
                $(`#y9`).val($(this).val())
                $(`#y10`).val($(this).val())
                $(`#y11`).val($(this).val())
                $(`#y12`).val($(this).val())
                $(`#y2-1`).val($(this).val())
                $(`#y2-2`).val($(this).val())
                $(`#y2-3`).val($(this).val())
                $(`#y2-4`).val($(this).val())
                $(`#y2-5`).val($(this).val())
            })



        });
        $(document).ready(function() {
            $('th').on('mousedown', function(e) {
                const th = $(this);
                const startX = e.pageX;
                const startWidth = th.width();
                const minWidth = 0; // Lebar minimum kolom yang diizinkan

                // Mengatur ukuran kolom saat mouse bergerak
                $(document).on('mousemove', function(e) {
                    const diff = e.pageX - startX;
                    const newWidth = startWidth + diff;

                    // Pastikan kolom tidak lebih kecil dari lebar minimum
                    if (newWidth > minWidth) {
                        th.width(newWidth);
                    }
                });

                // Mengakhiri proses resizing saat mouse dilepas
                $(document).on('mouseup', function() {
                    $(document).off('mousemove mouseup');
                });
            });
        });
    </script>


</x-Layout.layout>
