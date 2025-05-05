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


        <x-slot:tittle>Menu Jurnal</x-slot:tittle>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <p class="server-time">Tanggal Server: {{ date('Y-m-d H:i:s') }}</p>
        <div class="overflow-x-auto mt-8">
            <a href="{{ route('jurnal-manual.index') }}">
                <button class="btn bg-green-500 text-white font-bold hover:bg-green-700 m-2x">Input Jurnal</button>
            </a>
            <a href="{{ route('jurnal.jurnal-merger') }}">
                <button class="btn bg-gray-500 text-white font-bold hover:bg-gray-700 mb-2px">Merge Jurnal</button>
            </a>
            
            <button class="btn bg-red-500 text-white font-bold hover:bg-gray-700 mb-2px" id="ipt_jurnal">Export
                Jurnal</button>
                <a href="/invoice-external">
                    <button class="btn bg-blue-500 text-white font-bold hover:bg-gray-700">Penerimaan Tagihan</button>
                </a>

            <div class="flex flex-row mb-16 mt-8">
                <label for="month" class="font-bold mt-2">Bulan:</label>
                @for ($i = 1; $i <= 12; $i++)
                    @php
                        $monthName = date('M', mktime(0, 0, 0, $i, 1));
                    @endphp
                    <form action="" method="GET">
                        <input type="hidden" name="month" value="{{ $i }}">
                        <input type="hidden" name="year" value="{{ date('Y') }}"class="year-input">
                        <button
                            class="px-3 py-2 border-2 border-green-300 hover:bg-green-300 hover:text-white duration-300 rounded-xl mx-1 
                                {{ request('month', date('n')) == $i ? 'bg-green-300 text-white' : '' }}">
                            {{ $monthName }}
                        </button>
                    </form>
                @endfor

                <div class="w-full ml-10 mt-3">
                    <b>Tahun : </b>
                    <select id="thn" class="w-1/2 select2">
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
                            $types = ['JNL']; // Daftar tipe
                        @endphp

                        @foreach ($types as $type)
                            <form action="" method="GET" class="inline-block">
                                <input type="hidden" name="tipe" value="{{ $type }}">
                                <input type="hidden" name="month" value="{{ request('month') ?? date('m') }}">
                                <input type="hidden" name="year" value="{{ request('year') ?? date('Y') }}" class="year-input">
                                <button type="submit"
                                    class="px-3 py-2 border-2 border-green-300 hover:bg-green-300 hover:text-white duration-300 rounded-xl mx-1
            {{ request('tipe') == $type ? 'bg-green-300 text-white' : '' }}">
                                    {{ $type }}
                                </button>
                            </form>
                        @endforeach
                        <form action="" method="GET" class="inline-block">
                            <input type="hidden" name="kas" value="kas">
                            <input type="hidden" name="month" value="{{ request('month') ?? date('m') }}">
                            <input type="hidden" name="year" value="{{ request('year') ?? date('Y') }}" class="year-input">
                            <button type="submit"
                                class="px-3 py-2 border-2 border-green-300 hover:bg-green-300 hover:text-white duration-300 rounded-xl mx-1
        {{ request('kas') == 'kas' ? 'bg-green-300 text-white' : '' }}">
                                Kas
                            </button>
                        </form>
                        <form action="" method="GET" class="inline-block">
                            <input type="hidden" name="bank" value="bank">
                            <input type="hidden" name="month" value="{{ request('month') ?? date('m') }}">
                            <input type="hidden" name="year" value="{{ request('year') ?? date('Y') }}" class="year-input">
                            <button type="submit"
                                class="px-3 py-2 border-2 border-green-300 hover:bg-green-300 hover:text-white duration-300 rounded-xl mx-1
        {{ request('bank') == 'bank' ? 'bg-green-300 text-white' : '' }}">
                                Bank
                            </button>
                        </form>
                        <form action="" method="GET" class="inline-block">
                            <input type="hidden" name="ocbc" value="ocbc">
                            <input type="hidden" name="month" value="{{ request('month') ?? date('m') }}">
                            <input type="hidden" name="year" value="{{ request('year') ?? date('Y') }}" class="year-input">
                            <button type="submit"
                                class="px-3 py-2 border-2 border-green-300 hover:bg-green-300 hover:text-white duration-300 rounded-xl mx-1
        {{ request('ocbc') == 'ocbc' ? 'bg-green-300 text-white' : '' }}">
                                OCBC
                            </button>
                        </form>
                    </div>
                </div>
                <div>
                    <form action="{{ route('jurnal.edit') }}" method="get" class="ml-10">
                        <input type="hidden" name="nomor" id="nomor">
                        <button class="btn-coklat" id="edit">Edit Jurnal</button>

                    </form>
                </div>
            </div>

            <div class="bg-gray-50 mt-5 p-4 rounded shadow-sm">
                <div class="mb-4">
                    <label for="searchByNomor" class="font-weight-bold mr-2">Cari Berdasarkan Nomor Voucher:</label>
                    <input type="text" id="searchByNomor" class="input-field form-control"
                        placeholder="Masukkan nomor jurnal...">
                </div>
                <div class="mb-4">
                    <label for="searchByKeterangan" class="font-weight-bold mr-2">Cari Berdasarkan Keterangan:</label>
                    <input type="text" id="searchByKeterangan" class="input-field form-control"
                        placeholder="Masukkan keterangan jurnal...">
                </div>
            </div>


            <div class="dataTable-container">
                <table id="coa_table" class="dataTable cell-border hover display nowrap compact">
                    <!-- head -->
                    <thead>
                        <tr>
                            <th class="text-center">Tanggal
                            </th>
                            <th class="text-center">Tipe
                            </th>
                            <th class="text-center">Nomor
                            </th>
                            <th class="text-center">No. Akun
                            </th>
                            <th class="text-center">Nama Akun
                            </th>
                            <th class="text-center">Invoice
                            </th>
                            <th class="text-center">Debit
                            </th>
                            <th class="text-center">Kredit
                            </th>
                            <th class="text-center">Keterangan
                            </th>
                            <th class="text-center">Invoice Supplier
                            </th>
                            <th class="text-center">Nopol
                            </th>
                            <th class="text-center">Kaitan BB Pembantu
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
            $('.select2').select2();
            var table = $('#coa_table').DataTable({
                pageLength: 20, // Batas data per halaman
                lengthMenu: [20, 50, 100, 150], // Pilihan jumlah data per halaman
                ordering: false,
                scrollX: true,
            });
            var MonJNL = $('#monitoring_JNL').DataTable({})

            $('#searchByNomor').on('keyup', function() {
                table.columns(2).search(this.value).draw();
            });
            $('#searchByKeterangan').on('keyup', function() {
                table.columns(8).search(this.value).draw();
            });
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

            // Mengupdate nilai tahun untuk semua input dengan kelas 'year-input'
            $('#thn').on('change', function() {
                var selectedYear = $(this).val();

                // Mengupdate nilai 'year' pada semua input hidden dengan kelas 'year-input'
                $('.year-input').val(selectedYear);

                // Update nilai untuk elemen lain yang diperlukan (misal ID: y1, y2, y3, ...)
                for (var i = 1; i <= 12; i++) {
                    $('#y' + i).val(selectedYear);
                }

                // Update untuk elemen yang memiliki ID seperti y2-1, y2-2, ...
                for (var j = 1; j <= 5; j++) {
                    $('#y2-' + j).val(selectedYear);
                }
            });



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
        $('#ipt_jurnal').click(function() {
            $('#exp-jurnal').html(`
            <dialog id="my_modal_5" class="modal">
                <div class="modal-box w-11/12 max-w-2xl pl-10">
                    <form method="dialog">
                        <button type="button" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" id="close-modal">✕</button>
                    </form>
                    <h3 class="text-lg font-bold mt-2">Export Jurnal</h3>
                    <form id="jurhutForm" action="{{ route('jurnal.export') }}" method="post" onsubmit="return validateForm()">
                        @csrf
                        <label class="form-label">Mulai Tanggal</label>
                        <input type="date" id="mulai" name="mulai" class="rounded-md input-field"></td>
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" id="sampai" name="sampai" class="rounded-md input-field"></td>
                           <button type="submit" class="submit-button mb-5">
    <i class="fas fa-share"></i> Export
</button>
                        </div>
                    </form>
                </div>
            </dialog>
        `);

            // Menampilkan modal
            document.getElementById('my_modal_5').showModal();
            $('#close-modal').click(function() {
                document.getElementById('my_modal_5').close(); // Menutup modal
            });
        });
    </script>


</x-Layout.layout>
