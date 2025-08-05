<x-Layout.layout>

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

        /* Mengatur tabel dengan border minimal */
        .centered-container {
            width: 100%;
            margin: 0 auto;
            /* Untuk memusatkan secara horizontal */
            text-align: center;
        }

        #maintable {
            width: 100%;
            /* Ikuti lebar parent */
            margin-bottom: 20px;
        }

        /* Mengatur padding dan border untuk header tabel (th) */
        #maintable th {
            border: 1px solid black;
            /* Garis tepi untuk header */
            padding: 2px;
            /* Mengurangi padding menjadi lebih kecil */
            white-space: nowrap;
            /* Mencegah teks header terpotong ke baris baru */
            background-color: #f0f0f0;
            /* Warna latar belakang header */
            text-align: left;
            /* Rata kiri teks header */
        }

        /* Mengatur padding dan border untuk isi tabel (td) */
        #maintable td {
            border: 1px solid black;
            /* Garis tepi untuk sel */
            padding: 2px;
            /* Mengurangi padding agar lebih rapat */
            white-space: nowrap;
            /* Mencegah teks terpotong ke baris baru */
            line-height: 1;
            /* Membuat tinggi sel seminimal mungkin */
        }

        .grid-cols-3 .form-control {
            margin-bottom: 4px;
            /* Mengurangi jarak antar form */
        }

        .grid-cols-3 .input,
        .grid-cols-3 .select {
            padding: 4px;
            /* Atur padding agar elemen tidak terlalu lebar */
        }

        /* Gaya untuk Select2 */
        .select2-container--default .select2-selection--single {
            border: 1px solid #d1d5db;
            /* Warna border */
            border-radius: 0.375rem;
            /* Radius sudut */
            height: 2.5rem;
            /* Tinggi */
            /* Padding dalam */
            font-size: .9rem;
            /* Ukuran font */
        }

        /* Gaya untuk teks yang dipilih */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #374151;
            /* Warna teks */
            line-height: 2.5rem;
            /* Tinggi baris */
        }

        /* Gaya untuk ikon dropdown */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            /* Tinggi penuh */
            /* Jarak kanan */
        }

        /* Gaya untuk opsi dropdown */
        .select2-container--default .select2-results__option {
            color: #374151;
            /* Warna teks untuk opsi */
        }

        /* Gaya hover untuk opsi dropdown */
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #46e570;
            /* Warna latar saat hover */
            color: white;
            /* Warna teks saat hover */
        }

        /* Gaya untuk menengahkan teks dalam Select2 */
        .select2-container--default .select2-selection--single .select2-selection__rendered {

            /* Menengahkan secara horizontal */
            height: 100%;
            /* Tinggi penuh */
        }

        /* Gaya untuk opsi dropdown */


        /* Gaya hover untuk opsi dropdown */
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #359c08;
            /* Warna latar saat hover */
            color: white;
            /* Warna teks saat hover */
        }
    </style>
    <!-- Link CSS untuk jqGrid dan jQuery UI -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    <!-- jQuery dulu -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- jQuery UI (jika diperlukan jqGrid versi lama) -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <!-- CSS jqGrid -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/free-jqgrid@4.15.5/css/ui.jqgrid.min.css">

    <!-- JS jqGrid -->
    <script src="https://cdn.jsdelivr.net/npm/free-jqgrid@4.15.5/js/jquery.jqgrid.min.js"></script>
.

    <!-- Card untuk tampilan laporan piutang -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}">

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Rekap Pembayaran Harian</x-slot:tittle>

        <div class="centered-container">
            <form action=
            "{{ route('monitorInv.store') }}" method="POST" id="form-jurnal">
                @csrf
                <div class="grid grid-cols-2 gap-4 justify-items-start mt-5 mb-5">
                    <button id="addRow" type="button" class="btn bg-blue-400 text-white ">Tambah Baris</button>
                    <label class="form-control w-full max-w-xs mb-1">
                        <div class="label">
                            <span class="label-text text-red-600 font-bold">Pilih Tanggal Masuk Rekening</span>
                        </div>
                        <input type="date"
                            class="input input-sm input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                            id="tanggal_bayar" name="tanggal_bayar" autocomplete="off" value="{{ date('Y-m-d') }}" />
                    </label>
                </div>

                <table class="table" id="maintable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice</th>
                            <th>Pembayaran</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr id="row-0">
                            <td>1</td>
                            <td>
                                <select name="invoice[0]" id="invoice[0]" class="select select-bordered w-full">
                                    <option value="" selected></option>
                                    @foreach ($invoices as $item)
                                        <option value="{{ $item->id }}" data-subtotal="{{ $item->total_subtotal }}">
                                            {{ $item->invoice }} || {{ number_format($item->total_subtotal) }} ||
                                            {{ $item->customer }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="lunas[0]" class="select select-bordered w-full"
                                    onchange="nominal(this, 0)">
                                    <option value="" selected>Pilih Pembayaran</option>
                                    <option value="lunas">Lunas</option>
                                    <option value="cicilan">Cicilan</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" id="nominal[0]" name="nominal[0]" min="0"
                                    class="input input-sm input-bordered w-full" oninput="formatRibuan(this)" />

                            </td>

                        </tr>
                    </tbody>
                </table>

                <button type="submit" class="submit-button mt-8">Simpan</button>
            </form>
        </div>
    </x-keuangan.card-keuangan>

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Riwayat Rekap Harian</x-slot:tittle>
        {{-- <div class="grid grid-cols-2 gap-4 justify-items-start mt-5 mb-5">
            <label class="form-control w-full max-w-xs mb-1">
                <div class="label">
                    <span class="label-text">Cari Tanggal Bayar</span>
                </div>
                <input type="date"
                    class="input input-sm input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                    id="tanggal_bayar1" name="tanggal_bayar1" autocomplete="off" />
            </label>
        </div> --}}
        @php
            $filteredLebihBayar = collect($invoiceLebihBayar)->filter(function ($item) {
                return $item['selisih'] > 0;
            });
        @endphp

        @if ($filteredLebihBayar->count())
            <h3 style="margin-top: 20px; color: #c0392b;">Kelebihan Bayar</h3>

            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="border: 1px solid #ccc; padding: 8px;">Invoice</th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">Total Tagihan (Incl. PPN)
                        </th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">Total Dibayar</th>
                        <th style="border: 1px solid #ccc; padding: 8px; text-align: right;">Selisih Lebih</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoiceLebihBayar as $item)
                        <tr>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{ $item['invoice'] }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px; text-align: right;">
                                {{ $item['total_tagihan'] }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px; text-align: right;">
                                {{ $item['total_bayar'] }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px; text-align: right; color: red;">
                                {{ $item['selisih'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif


        <table id="biayaGrid1"></table>
        <div id="biayaPager1"></div>
    </x-keuangan.card-keuangan>

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Detail Rekap Harian</x-slot:tittle>
        <div class="grid grid-cols-2 gap-4 justify-items-start mt-5 mb-5">
            <label class="form-control w-full max-w-xs mb-1">
                <div class="label">
                    <span class="label-text">Cari Tanggal Masuk Rekening</span>
                </div>
                <input type="date"
                    class="input input-sm input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                    id="tanggal_bayar1" name="tanggal_bayar1" autocomplete="off" value="{{ date('Y-m-d') }}" />
            </label>
            <label class="form-control w-full max-w-xs mb-1">
                <div class="label">
                    <span class="label-text">Cek invoice di tgl tersebut</span>
                </div>
                <input type="text"
                    class="input input-sm input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                    id="inv" name="inv" autocomplete="off" />
            </label>
        </div>
        <table id="biayaGrid"></table>
        <div id="biayaPager"></div>
    </x-keuangan.card-keuangan>
</x-Layout.layout>



<script>
    $(document).ready(function() {
        // Trigger filter saat tanggal bayar diubah
        $('#tanggal_bayar1').on('change', function() {
            $("#biayaGrid").jqGrid('setGridParam', {
                datatype: 'json',
                postData: {
                    tgl_pembayar3: $(this).val()
                },
                page: 1
            }).trigger('reloadGrid');
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Trigger filter saat tanggal bayar diubah
        $('#inv').on('change', function() {
            $("#biayaGrid").jqGrid('setGridParam', {
                datatype: 'json',
                postData: {
                    inv3: $(this).val()
                },
                page: 1
            }).trigger('reloadGrid');
        });
    });
</script>
<script>
    $(function() {
        function resizeGrid() {
            $("#biayaGrid").setGridWidth($('#biayaGrid').closest(".ui-jqgrid").parent().width(), true);
        }

        $("#biayaGrid").jqGrid({
            url: '{{ route('biaya.monitoring.data') }}',
            datatype: "json",
            mtype: "GET",
            postData: {
                tgl_pembayar3: function() {
                    return $('#tanggal_bayar1').val();
                },
                inv3: function() {
                    return $('#inv').val();
                }
            },
            colModel: [{

                    label: 'Tanggal Masuk Rekening',
                    name: 'tgl_pembayar',
                    align: 'center',
                    width: 100,
                    formatter: 'date',
                    formatoptions: {
                        newformat: 'd/m/Y'
                    }
                },
                {
                    label: 'Customer',
                    name: 'customer',
                    width: 150,
                },
                {
                    label: 'Invoice',
                    name: 'invoice',
                    width: 120,
                },
                {

                    label: 'Terbayar',
                    name: 'bayar',
                    width: 120,
                    align: 'right',
                    formatter: 'currency',
                    formatoptions: {
                        thousandsSeparator: ".",
                        decimalSeparator: ",",
                        decimalPlaces: 0,
                    },
                    summaryType: 'sum'
                },
                {

                    label: 'Aksi',
                    name: 'aksi',
                    width: 100,
                    align: 'center',
                    sortable: false,
                    formatter: function(cellValue, options, rowObject) {
                        if (rowObject.jurnal === 'Belum Terjurnal') {
                            return `<button class="bg-red-500 hover:bg-red-300 text-white font-semibold py-1 px-2 rounded btn-delete" data-id="${rowObject.id}">Hapus</button>`;
                        } else {
                            return ''; // atau return '—';
                        }
                    }
                }
            ],
            jsonReader: {
                root: "rows",
                page: "page",
                total: "total",
                records: "records",
                repeatitems: false,
                id: "id",
                userdata: "userdata"
            },
            pager: "#biayaPager",
            rowNum: 10,
            rowList: [10, 20, 50],
            height: '100%',
            autowidth: true,
            shrinkToFit: true,
            viewrecords: true,
            footerrow: true,
            userDataOnFooter: true, // Aktifkan userdata ke footer
            caption: "Data Pembayaran Invoice",
            loadComplete: function() {
                resizeGrid();
            }
        });

        $(window).on('resize', function() {
            resizeGrid();
        });

        //   $("#biayaGrid").jqGrid('filterToolbar', {
        //     searchOperators: false,
        //     searchOnEnter: false,
        //     defaultSearch: "cn"
        // });
    });



    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        if (confirm('Yakin ingin menghapus data ini?')) {
            $.ajax({
                url: `/laporan/biaya-inv/${id}`, // Ganti sesuai route delete-mu
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alert('Data berhasil dihapus!');
                    $("#biayaGrid1").trigger('reloadGrid');
                    $("#biayaGrid").trigger('reloadGrid');
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghapus data.');
                }
            });
        }
    });
    $(function() {
        function resizeGrid() {
            $("#biayaGrid1").setGridWidth($('#biayaGrid1').closest(".ui-jqgrid").parent().width(), true);
        }

        $("#biayaGrid1").jqGrid({
            url: '{{ route('list.inv.data') }}',
            datatype: "json",
            mtype: "GET",
            colModel: [{
                    label: 'No',
                    name: 'DT_RowIndex',
                    width: 50,
                    key: true,
                    align: 'center',
                    search: true
                },
                {
                    label: 'Invoice',
                    name: 'invoice',
                    align: 'center',
                    width: 150,
                    search: true
                },
                {
                    label: 'Customer',
                    name: 'customer',
                    align: 'center',
                    width: 150,
                    search: true
                },
                {
                    label: 'Tgl Invoice',
                    name: 'tgl_inv',
                    align: 'center',
                    width: 150,
                    search: true
                },
                {
                    label: 'Nilai Inv',
                    name: 'total',
                    width: 150,
                    align: 'right',
                    search: true
                },
                {
                    label: 'Tgl Masuk Rekening',
                    name: 'tgl_pembayar',
                    align: 'center',
                    width: 150,
                    search: true
                },
                {
                    label: '(Rp) Bayar',
                    name: 'nominal',
                    align: 'right',
                    width: 150,
                    search: true
                },
                {
                    label: 'Sisa',
                    name: 'sisa',
                    width: 150,
                    align: 'right',
                    search: true
                }
            ],
            jsonReader: {
                root: "rows",
                page: "page",
                total: "total",
                records: "records",
                repeatitems: false,
                id: "id"
            },
            pager: "#biayaPager1",
            rowNum: 10,
            rowList: [10, 20, 50],
            height: 'auto', // Jika ingin tinggi dinamis
            autowidth: true, // Otomatis sesuaikan lebar
            shrinkToFit: true, // Kolom menyesuaikan grid
            viewrecords: true,
            multiselect: false,
            caption: "Data Pembayaran Invoice",
            loadComplete: function() {
                resizeGrid();
            }
        });

        // Toolbar filter
        $("#biayaGrid1").jqGrid('filterToolbar', {
            searchOperators: false,
            searchOnEnter: false,
            defaultSearch: "cn"
        });

        // Otomatis resize saat window diubah
        $(window).bind('resize', function() {
            resizeGrid();
        });
    });
</script>
<script>
    function formatRibuan(input) {
        let value = input.value.replace(/\D/g, ''); // Menghapus semua karakter selain angka
        value = Number(value); // Mengonversi nilai menjadi angka

        // Menambahkan pemisah ribuan
        if (!isNaN(value)) {
            input.value = value.toLocaleString(); // Format angka dengan pemisah ribuan
        }
    }


    function nominal(select, rowId) {
        // Mendapatkan nilai yang dipilih dari select
        const selectedValue = select.value;

        // Menemukan elemen nominal berdasarkan rowId
        const nominal = document.getElementById(`nominal[${rowId}]`);

        // Menemukan select berdasarkan rowId dan mendapatkan option yang dipilih
        const selectedOption = document.querySelector(`select[name="invoice[${rowId}]"] option:checked`);

        // Mendapatkan subtotal dari atribut data-subtotal pada option yang dipilih
        let subtotal = selectedOption ? selectedOption.getAttribute('data-subtotal') : 0;

        // Mengubah subtotal menjadi angka, membulatkan, dan menambahkan pemisah ribuan
        subtotal = Number(subtotal); // Mengonversi menjadi angka
        subtotal = Math.round(subtotal); // Pembulatan angka // Menambahkan pemisah ribuan
        subtotal = subtotal.toLocaleString(); // Menambahkan pemisah ribuan

        console.log('Subtotal:', subtotal, selectedOption);

        // Jika nilai select adalah "lunas", mengubah nilai nominal dan menambahkan atribut readonly pada elemen nominal
        if (selectedValue == "lunas") {
            // Jika elemen nominal ditemukan, set nilai nominal dengan subtotal
            if (nominal) {
                nominal.value = subtotal;

                // Menambahkan atribut readonly pada elemen nominal
                nominal.setAttribute('readonly', 'true');
            }
        } else if (selectedValue == "cicilan") {
            // Jika nilai select bukan "lunas", hapus atribut readonly pada elemen nominal
            nominal.value = 0;
            nominal.removeAttribute('readonly');
        }
    }



    let rowIndex = 1;
    $('select.select').select2({
        width: '100%'
    });

    const invoices = @json($invoices); // Kirim data invoice ke JS

    document.getElementById('addRow').addEventListener('click', function() {
        const tableBody = document.getElementById('tableBody');

        const row = document.createElement('tr');
        row.id = `row-${rowIndex}`;

        const selectOptions = invoices.map(inv =>
            `<option value="${inv.id}" data-subtotal="${inv.total_subtotal}">${inv.invoice} || ${inv.total_subtotal.toLocaleString()} || ${inv.customer.toLocaleString()}</option>`
        ).join('');

        row.innerHTML = `
        <td>${rowIndex + 1}</td>
        <td>
            <select name="invoice[${rowIndex}]"  class="select select-bordered w-full">
                <option value="" selected></option>
                ${selectOptions}
            </select>
        </td>
        <td>
            <select name="lunas[${rowIndex}]" class="select select-bordered w-full" onchange="nominal(this, ${rowIndex})">
                                    <option value="" selected>Pilih Pembayaran</option>
                                    <option value="lunas">Lunas</option>
                                    <option value="cicilan">Cicilan</option>
                                </select>
        </td>
        <td>
            <input type="text" id="nominal[${rowIndex}]"]" name="nominal[${rowIndex}]"  oninput="formatRibuan(this)" min="0" class="input input-sm input-bordered w-full" />
        </td>
    `;

        tableBody.appendChild(row);

        // Apply Select2 AFTER the element is appended to the DOM
        $(`#${row.id} select.select`).select2({
            width: '100%'
        });
        rowIndex++;
    });
</script>
