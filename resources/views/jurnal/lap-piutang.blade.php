<x-Layout.layout>

    <style type="text/css">
        /* CSS untuk memberikan jarak antar invoice */
        .nilai_invoice {
            line-height: 1.5;
            /* Jarak antar baris */
            margin-bottom: 5px;
            /* Jarak bawah antar invoice */
        }

        .server-time {
            font-size: 18px;
            margin-bottom: 15px;
            margin-top: 15px;
            margin-left: 400px;
            font-weight: bold;
            color: #ffffff;
            background-color: #3fae43;
            padding: 10px 15px;
            border-radius: 5px;
            display: inline-block;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
        }

        .table-container {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 500px;
            /* Sesuaikan tinggi maksimum agar tidak terlalu panjang */
            margin-top: 20px;
            border: 1px solid #ddd;
        }

        .table-cus {
            border-collapse: collapse;
            width: 100%;
            min-width: 800px;
            /* Pastikan tabel tidak terlalu sempit */
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            font-size: 12px;
        }

        .table-cus th,
        .table-cus td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            transition: background-color 0.3s ease;
        }

        .table-cus th {
            background-color: #7b7d80;
            color: white;
            position: sticky;
            top: 0;
            z-index: 2;
            border: 1px solid #ddd;
        }

        .table-cus td {
            background-color: #f9f9f9;
        }

        .table-cus tr:hover {
            background-color: #f1f1f1;
        }

        .table-cus .bg-thn {
            background-color: #6e791c;
            font-weight: bold;
            color: white;
        }

        .table-cus .sticky-footer {
            position: sticky;
            bottom: 0;
            background-color: #625a5a;
            /* Sesuaikan warna agar tidak menutupi konten */
            font-weight: bold;
            z-index: 2;
        }

        .sticky-footer {
            position: sticky;
            top: 35px;
            /* sesuaikan ini dengan tinggi header di atasnya */
            background-color: #625a5a;
            /* pastikan background tidak transparan */
            z-index: 5;
            /* lebih tinggi dari body */
        }

        thead tr:nth-child(1) th {
            position: sticky;
            top: 2px;
            z-index: 7;
            background: #625a5a;
        }

        thead tr:nth-child(2) th {
            position: sticky;
            top: 35px;
            z-index: 6;
            background: #625a5a;
        }

        thead tr:nth-child(3) th {
            position: sticky;
            top: 70px;
            z-index: 5;
            background: #625a5a;
        }

        .form-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            flex: 1 1 30%;
            /* tiga kolom */
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 0.5rem;
            font-weight: bold;
        }


        .form-group input,
        .form-group select {
            padding: 0.1rem;
            font-size: 1rem;
        }

        .form-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: end;
            margin-bottom: 1.5rem;
        }

        .form-filter .form-group {
            display: flex;
            flex-direction: column;
            flex: 1 1 200px;
            /* fleksibel tapi min lebar */
        }

        .form-filter .form-group label {
            font-weight: bold;
            margin-bottom: 0.25rem;
        }

        .form-filter .form-group select,
        .form-filter .form-group input {
            padding: 0.4rem 0.6rem;
            font-size: 0.9rem;
        }

        .form-filter .btn-filter {
            padding: 0.4rem 1rem;
            font-size: 0.9rem;
            height: auto;
            align-self: flex-start;
        }

        .button-group {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }

        .btn {
            padding: 6px 12px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .btn-red {
            background-color: #dc3545;
        }

        .btn-red:hover {
            background-color: #bb2d3b;
        }

        .btn-yellow {
            background-color: #ffc107;
            color: #000;
        }

        .btn-yellow:hover {
            background-color: #e0a800;
        }

        .btn-green {
            background-color: #28a745;
        }

        .btn-green:hover {
            background-color: #218838;
        }


        .btn-gray {
            background-color: #6c757d;
        }

        .btn-gray:hover {
            background-color: #5a6268;
        }

        .btn-gray-filter {
            width: 80px;
            background-color: #6c757d;
        }

        .btn-gray-filter:hover {
            background-color: #5a6268;
        }






        .table-cus .bg-total1 {
            background-color: #473f39;
            color: white;
            font-weight: bold;
            text-align: right;
        }

        .table-cus .bg-total-thn {
            background-color: #0f2d44;
            color: white;
            font-weight: bold;
        }

        .table-cus .bg-total-monthly {
            background-color: #098e0c;
            color: white;
            font-weight: bold;
            text-align: right;
        }

        .table-cus .bg-total-monthly1 {
            background-color: #546816;
            color: white;
            font-weight: bold;
            text-align: right;
        }

        .table-cus .bg-total {
            background-color: #93685b;
            color: white;
            font-weight: bold;
        }

        .table-cus .kurang-bayar {
            background-color: #f8f81f;
            color: rgb(0, 0, 0);
            font-weight: bold;
        }

            #customer.select2-hidden-accessible + .select2-container .select2-selection--single {
        height: 45px !important;
    }

    #customer.select2-hidden-accessible + .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 45px !important;
    }

    #customer.select2-hidden-accessible + .select2-container .select2-selection--single .select2-selection__arrow {
        height: 45px !important;
    }

        .loading-wrapper {
        padding: 40px;
        text-align: center;
        animation: fadeIn 0.5s ease-in-out;
    }

    .spinner {
        border: 4px solid #f3f3f3; /* Light grey */
        border-top: 4px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
        margin: auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    </style>
    <!-- Link CSS untuk jqGrid dan jQuery UI -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />



    <!-- Card untuk tampilan laporan piutang -->
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Report Outstanding Piutang Cust</x-slot:tittle>
        <p class="server-time">
            Tanggal dan Waktu Server : <span id="server-time">{{ now()->format('Y-m-d H:i:s') }}</span>
        </p>
        <div class="card shadow-sm border-0" style="background-color: #f8f9fa;">
            <div class="card-body">
                <strong class="mb-2 d-block">Keterangan Warna:</strong>
                <ul style="list-style: none; padding-left: 0; font-size: 0.85rem; margin: 0;">
                    <li class="mb-1">
                        <span
                            style="display:inline-block;width:15px;height:15px;background-color:#3fae43;border-radius:3px;margin-right:5px;"></span>
                        <span>Hijau - Lunas</span>
                    </li>
                    <li class="mb-1">
                        <span
                            style="display:inline-block;width:15px;height:15px;background-color:#ffd503;border-radius:3px;margin-right:5px;"></span>
                        <span>Kuning - Jatuh Tempo Dalam 1-4 Hari</span>
                    </li>
                    <li>
                        <span
                            style="display:inline-block;width:15px;height:15px;background-color:red;border-radius:3px;margin-right:5px;"></span>
                        <span>Merah - Lewat Jatuh Tempo</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="flex flex-row gap-4 mb-16 mt-8">

            <label class="form-control w-full max-w-xs mb-1">
                <div class="label">
                    <span class="label-text">Bulan Inv</span>
                </div>
                <input type="month"
                    class="input input-sm input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                    id="tgl_inv" name="tgl_inv" autocomplete="off" value="{{ date('Y-m') }}" />
            </label>

            <label class="form-control w-full max-w-xs mb-1">
                <div class="label">
                    <span class="label-text">Cari No Invoice di bulan tersebut</span>
                </div>
                <input type="text"
                    class="input input-sm input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                    id="inv" name="inv" autocomplete="off" />
            </label>

             <label class="form-control w-full max-w-xs mb-1">
                <div class="label">
                    <span class="label-text">Cari Customer</span>
                </div>
                 <select name="customer" id="customer" class="select2">
                        <option></option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" {{ request('customer') == $c->id ? 'selected' : '' }}>
                                {{ $c->nama }}
                            </option>
                        @endforeach
                    </select>
            </label>

            <div class="col-md-6 mb-5 text-end">
                <label class="form-label d-block">&nbsp;</label> {{-- spacing --}}
                <div class="d-flex gap-2 mb-2">
                    <button class="btn btn-red" onclick="filterWarna('merah')">Merah</button>
                    <button class="btn btn-yellow" onclick="filterWarna('kuning')">Kuning</button>
                    <button class="btn btn-green" onclick="filterWarna('hijau')">Hijau</button>
                    <button class="btn btn-gray" onclick="filterWarna('')">Reset</button>
                </div>

            </div>
        </div>


        <!-- Tabel untuk menampilkan data menggunakan jqGrid -->
        <table class="table" id="table-lp"></table>

        <!-- Pager untuk navigasi halaman -->
        <div id="jqGridPager"></div>
    </x-keuangan.card-keuangan>


     <x-keuangan.card-keuangan>
        <x-slot:tittle>Lap Piutang Cari Kurang Bayar</x-slot:tittle>
    <div class="flex flex-row gap-4 mb-2 mt-5">
            <label class="form-control w-full max-w-xs mb-1">
                <div class="label">
                    <span class="label-text">Cari Nominal Bayar</span>
                </div>
                <input type="text"
                    class="input input-sm input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
                    id="nominal" name="nominal" autocomplete="off" />
            </label>
        </div>
        <!-- Tabel untuk menampilkan data menggunakan jqGrid -->
        <table class="table" id="table-lp1"></table>

        <!-- Pager untuk navigasi halaman -->
        <div id="jqGridPager1"></div>
    </x-keuangan.card-keuangan>

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Summary Laporan Piutang</x-slot:tittle>
        <!-- Tabel untuk menampilkan data menggunakan jqGrid -->
        <table class="table" id="table-lp-total"></table>

        <!-- Pager untuk navigasi halaman -->
        <div id="jqGridPagerTotal"></div>
    </x-keuangan.card-keuangan>

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Monitoring Piutang Customer</x-slot:tittle>

        <!-- Dropdown untuk memilih tahun -->
        <form id="filterForm">
    <div class="form-filter">
        {{-- Pilih Tahun --}}
        <div class="form-group">
            <label for="year">Pilih Tahun</label>
            <select name="year" id="year">
                <option value="" disabled {{ request('year') ? '' : 'selected' }}>Pilih Tahun</option>
                @foreach ($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>

        {{-- Pilih Customer --}}
        <div class="form-group">
            <label for="customers">Cari Berdasarkan Customer</label>
            <select name="customer" id="customers" class="select2">
                <option></option>
                @foreach ($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->nama }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tombol Filter --}}
        <div class="form-group">
            <button type="button" class="btn btn-gray-filter" id="applyFilter">Filter</button>
        </div>
    </div>
</form>




        <div class="table-container">
            <div id="table-container">
                @include('jurnal.partials.lap-piutang-table', [
                    'mergedResults' => $mergedResults,
                    'monthlyInvoiceCounts' => $monthlyInvoiceCounts,
                    'monthlySelisihInvoice' => $monthlySelisihInvoice,
                    'monthlyTotals' => $monthlyTotals,
                    'months' => $months,
                    'year' => request('year') ?? 2025
                ])
            </div>
        </div>
    </x-keuangan.card-keuangan>

    <!-- Script untuk memuat jqGrid -->
    <x-slot:script>
        <script type="text/javascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>
        <!-- Select2 JS (letakkan sebelum </body>) -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#customers').select2({
                    placeholder: 'Pilih Customer',
                    allowClear: true
                });
            });
        </script>

  <script>
            $(document).ready(function() {
                $('#customer').select2({
                    placeholder: 'Pilih Customer',
                    allowClear: true
                });
            });
        </script>
<script>
    $(document).ready(function () {
        $('#applyFilter').on('click', function () {
            const year = $('#year').val();
            const customer = $('#customers').val();

            // Munculkan animasi loading
            $('#table-container').fadeOut(200, function () {
                $('#table-container').html(`
                    <div class="loading-wrapper">
                        <div class="spinner"></div>
                        <div style="margin-top:10px;">Memuat data...</div>
                    </div>
                `).fadeIn(200);
            });

            // Kirim AJAX setelah jeda (delay 500ms)
            setTimeout(function () {
                $.ajax({
                    url: "{{ route('laporan.Piutang') }}",
                    type: "GET",
                    data: {
                        year: year,
                        customers: customer
                    },
                    success: function (response) {
                        $('#table-container').fadeOut(100, function () {
                            $(this).html(response.tableHtml).fadeIn(300);
                        });
                    },
                    error: function () {
                        $('#table-container').html('<p style="color:red; text-align:center;">Terjadi kesalahan saat memuat data.</p>');
                    }
                });
            }, 500);
        });
    });
</script>



        <script>
        $(document).ready(function () {
    function formatRibuan(angka) {
        return angka.replace(/\D/g, '')  // hanya angka
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function reloadGridWithFilters() {
        const tfMasukVal = $('#nominal').val().replace(/[^0-9]/g, '');
        console.log("Memuat ulang grid dengan tf_masuk:", tfMasukVal);

        $("#table-lp1").jqGrid('setGridParam', {
            datatype: 'json',
            postData: {
                nominal: tfMasukVal
            },
            page: 1
        }).trigger('reloadGrid');
    }

    // ✅ Format saat mengetik
    $('#nominal').on('input', function () {
        let val = $(this).val();
        let caretPos = this.selectionStart; // jaga posisi kursor
        let clean = val.replace(/\D/g, '');
        let formatted = formatRibuan(clean);
        $(this).val(formatted);

        // Kembalikan posisi kursor jika perlu
        this.setSelectionRange(caretPos, caretPos);
    });

    // ✅ Kirim hanya saat ENTER
    $('#nominal').on('keypress', function (e) {
        if (e.which === 13) {
            reloadGridWithFilters();
        }
    });
});


        </script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Fungsi untuk memperbarui waktu
                function updateServerTime() {
                    fetch("{{ route('server.time') }}")
                        .then(response => response.json()) // Mengambil data JSON dari server
                        .then(data => {
                            document.getElementById("server-time").textContent = data
                            .time; // Update elemen dengan id "server-time"
                        })
                        .catch(error => console.error('Error fetching server time:', error)); // Tangani error
                }

                // Perbarui waktu server pertama kali saat halaman dimuat
                updateServerTime();

                // Perbarui setiap detik
                setInterval(updateServerTime, 1000);
            });
        </script>
        <script>
            $(document).ready(function() {
                // Trigger filter saat tanggal bayar diubah
                $('#tgl_inv').on('change', function() {
                    $("#table-lp").jqGrid('setGridParam', {
                        datatype: 'json',
                        postData: {
                            tgl_inv: $(this).val()
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
                    $("#table-lp").jqGrid('setGridParam', {
                        datatype: 'json',
                        postData: {
                            inv: $(this).val()
                        },
                        page: 1
                    }).trigger('reloadGrid');
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                // Trigger filter saat tanggal bayar diubah
                $('#customer').on('change', function() {
                    $("#table-lp").jqGrid('setGridParam', {
                        datatype: 'json',
                        postData: {
                            customer: $(this).val()
                        },
                        page: 1
                    }).trigger('reloadGrid');
                });
            });
        </script>

        <script>
            function filterWarna(warna) {
                let grid = $("#table-lp");
                let postData = grid.jqGrid('getGridParam', 'postData');

                postData.filters = JSON.stringify({
                    groupOp: "AND",
                    rules: warna ? [{
                        field: "warna_status",
                        op: "eq",
                        data: warna
                    }] : []
                });

                grid.jqGrid('setGridParam', {
                    search: true,
                    postData: postData,
                    page: 1
                }).trigger("reloadGrid");
            }

            $(document).ready(function() {
                $("#table-lp").jqGrid({
                    url: "{{ route('laporan.DataPiutang') }}",
                    datatype: "json",
                    postData: {
                        tgl_inv: function() {
                            return $('#tgl_inv').val();
                        },

                        inv: function() {
                            return $('#inv').val();
                        },

                        customer: function() {
                            return $('#customer').val();
                        }
                    },
                    mtype: "GET",
                    colModel: [{
                            search: true,
                            label: 'Invoice',
                            name: 'invoice',
                            width: 100,
                            align: "center",
                            sortable: true
                        },
                        {
                            name: 'warna_status',
                            index: 'warna_status',
                            hidden: true, // Atau tampilkan jika mau
                            search: true,
                            stype: 'select',
                            searchoptions: {
                                sopt: ['eq'],
                                value: ':All;hijau:Hijau;oranye:Oranye;merah:Merah'
                            }
                        },

                        {
                            search: true,
                            label: 'Nama Customer',
                            name: 'customer',
                            width: 120,
                            align: "left",
                            sortable: true
                        },
                        {
                            search: true,
                            label: 'Harga (INC.PPN)',
                            name: 'jumlah_harga',
                            width: 120,
                            align: "right",
                            formatter: 'currency',
                            formatoptions: {
                                thousandsSeparator: ','
                            },
                            sortable: true
                        },
                        {
                            name: 'tanggal',
                            label: 'Tanggal',
                            width: 50,
                            align: "center",
                            formatter: 'date',
                            formatoptions: {
                                newformat: 'Y-m-d'
                            },
                            sortable: true,
                            hidden: true
                        },
                        {
                            search: true,
                            label: 'TGL Invoice',
                            name: 'ditagih_tgl',
                            width: 50,
                            align: "center",
                            formatter: 'date',
                            formatoptions: {
                                newformat: 'Y-m-d'
                            },
                            sortable: true
                        },
                        {
                            search: true,
                            label: 'TOP',
                            name: 'top',
                            width: 30,
                            align: "center",
                            sortable: true
                        },
                        {
                            search: true,
                            label: 'Jatuh Tempo TGL',
                            name: 'tempo',
                            width: 50,
                            align: "center",
                            formatter: 'date',
                            formatoptions: {
                                newformat: 'Y-m-d'
                            },
                            sortable: true
                        },
                        {
                            search: true,
                            label: 'Dibayar TGL',
                            name: 'dibayar_tgl',
                            width: 50,
                            align: "center",
                            sortable: true
                        },
                        {
                            search: true,
                            label: 'Dibayar',
                            name: 'sebesar',
                            width: 120,
                            align: "right",
                            formatter: 'currency',
                            formatoptions: {
                                thousandsSeparator: ','
                            },
                            sortable: true
                        },
                        {
                            search: true,
                            label: 'Kurang Bayar',
                            name: 'kurang_bayar',
                            width: 120,
                            align: "right",
                            formatter: 'currency',
                            formatoptions: {
                                thousandsSeparator: ','
                            },
                            sortable: true
                        }
                    ],
                    pager: "#jqGridPager",
                    rowNum: 150,
                    rowList: [150, 200],
                    viewrecords: true,
                    autowidth: true,
                    loadonce: false,
                    height: 'auto',
                    jsonReader: {
                        repeatitems: false,
                        root: "rows",
                        page: "page",
                        total: "total",
                        records: "records"
                    },
                    rowattr: function(rowData) {
                        if (!rowData.tempo) return {}; // Jika tidak ada tempo, tidak ada warna

                        let today = new Date().toISOString().split('T')[0]; // Format YYYY-MM-DD
                        let tempoDate = new Date(rowData.tempo).toISOString().split('T')[0];

                        let timeDiff = new Date(rowData.tempo) - new Date();
                        let daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

                        // Jika kurang bayar = 0, semua kondisi tetap hijau
                        if (parseFloat(rowData.kurang_bayar) === 0) {
                            return {
                                "style": "background-color: #3fae43; color: white;"
                            };
                        }

                        // Jika TOP = 0 dan jatuh tempo hari ini, tidak diberi warna
                        if (parseInt(rowData.top) === 0 && tempoDate === today) {
                            return {};
                        }

                        // Warna oranye untuk jatuh tempo dalam 1-3 hari
                        if (daysDiff > 0 && daysDiff <= 4) {
                            return {
                                "style": "background-color: orange;"
                            };
                        }

                        // Warna merah jika sudah jatuh tempo atau jatuh tempo hari ini
                        if (daysDiff < 0) {
                            return {
                                "style": "background-color: red; color: white;"
                            };
                        }

                        return {};
                    },

                });

                $("#table-lp").jqGrid('navGrid', '#jqGridPager', {
                    edit: false,
                    add: false,
                    del: false,
                    search: true,
                    refresh: true
                });

            });



            // function filterWarna(warna) {
            //     let grid = $("#table-lp1");
            //     let postData = grid.jqGrid('getGridParam', 'postData');

            //     postData.filters = JSON.stringify({
            //         groupOp: "AND",
            //         rules: warna ? [{
            //             field: "warna_status",
            //             op: "eq",
            //             data: warna
            //         }] : []
            //     });

            //     grid.jqGrid('setGridParam', {
            //         search: true,
            //         postData: postData,
            //         page: 1
            //     }).trigger("reloadGrid");
            // }

            $(document).ready(function() {
                $("#table-lp1").jqGrid({
                    url: "{{ route('laporan.DataPiutang') }}",
                    datatype: "json",
                    postData: {
                         nominal: function() {
                    return $('#nominal').val();
                },
                        null: true
                    },
                    mtype: "GET",
                    colModel: [{
                            search: true,
                            label: 'Invoice',
                            name: 'invoice',
                            width: 100,
                            align: "center",
                            sortable: true
                        },
                        {
                            name: 'warna_status',
                            index: 'warna_status',
                            hidden: true, // Atau tampilkan jika mau
                            search: true,
                            stype: 'select',
                            searchoptions: {
                                sopt: ['eq'],
                                value: ':All;hijau:Hijau;oranye:Oranye;merah:Merah'
                            }
                        },

                        {
                            search: true,
                            label: 'Nama Customer',
                            name: 'customer',
                            width: 120,
                            align: "left",
                            sortable: true
                        },
                        {
                            search: true,
                            label: 'Harga (INC.PPN)',
                            name: 'jumlah_harga',
                            width: 120,
                            align: "right",
                            formatter: 'currency',
                            formatoptions: {
                                thousandsSeparator: ','
                            },
                            sortable: true
                        },
                        {
                            name: 'tanggal',
                            label: 'Tanggal',
                            width: 50,
                            align: "center",
                            formatter: 'date',
                            formatoptions: {
                                newformat: 'Y-m-d'
                            },
                            sortable: true,
                            hidden: true
                        },
                        {
                            search: true,
                            label: 'TGL Invoice',
                            name: 'ditagih_tgl',
                            width: 50,
                            align: "center",
                            formatter: 'date',
                            formatoptions: {
                                newformat: 'Y-m-d'
                            },
                            sortable: true
                        },
                        {
                            search: true,
                            label: 'TOP',
                            name: 'top',
                            width: 30,
                            align: "center",
                            sortable: true
                        },
                        {
                            search: true,
                            label: 'Jatuh Tempo TGL',
                            name: 'tempo',
                            width: 50,
                            align: "center",
                            formatter: 'date',
                            formatoptions: {
                                newformat: 'Y-m-d'
                            },
                            sortable: true
                        },
                        {
                            search: true,
                            label: 'Dibayar TGL',
                            name: 'dibayar_tgl',
                            width: 50,
                            align: "center",
                            sortable: true
                        },
                        {
                            search: true,
                            label: 'Dibayar',
                            name: 'sebesar',
                            width: 120,
                            align: "right",
                            formatter: 'currency',
                            formatoptions: {
                                thousandsSeparator: ','
                            },
                            sortable: true
                        },
                        {
                            search: true,
                            label: 'Kurang Bayar',
                            name: 'kurang_bayar',
                            width: 120,
                            align: "right",
                            formatter: 'currency',
                            formatoptions: {
                                thousandsSeparator: ','
                            },
                            sortable: true
                        }
                    ],
                    pager: "#jqGridPager1",
                    rowNum: 150,
                    rowList: [150, 200],
                    viewrecords: true,
                    autowidth: true,
                    loadonce: false,
                    height: 'auto',
                    jsonReader: {
                        repeatitems: false,
                        root: "rows",
                        page: "page",
                        total: "total",
                        records: "records"
                    },
                    rowattr: function(rowData) {
                        if (!rowData.tempo) return {}; // Jika tidak ada tempo, tidak ada warna

                        let today = new Date().toISOString().split('T')[0]; // Format YYYY-MM-DD
                        let tempoDate = new Date(rowData.tempo).toISOString().split('T')[0];

                        let timeDiff = new Date(rowData.tempo) - new Date();
                        let daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

                        // Jika kurang bayar = 0, semua kondisi tetap hijau
                        if (parseFloat(rowData.kurang_bayar) === 0) {
                            return {
                                "style": "background-color: #3fae43; color: white;"
                            };
                        }

                        // Jika TOP = 0 dan jatuh tempo hari ini, tidak diberi warna
                        if (parseInt(rowData.top) === 0 && tempoDate === today) {
                            return {};
                        }

                        // Warna oranye untuk jatuh tempo dalam 1-3 hari
                        if (daysDiff > 0 && daysDiff <= 4) {
                            return {
                                "style": "background-color: orange;"
                            };
                        }

                        // Warna merah jika sudah jatuh tempo atau jatuh tempo hari ini
                        if (daysDiff < 0) {
                            return {
                                "style": "background-color: red; color: white;"
                            };
                        }

                        return {};
                    },

                });

                $("#table-lp1").jqGrid('navGrid', '#jqGridPager1', {
                    edit: false,
                    add: false,
                    del: false,
                    search: true,
                    refresh: true
                });

            });
        </script>

        <script>
            $(document).ready(function() {
                $("#table-lp-total").jqGrid({
                    url: "{{ route('laporan.TotalDataPiutang') }}",
                    datatype: "json",
                    mtype: "GET",
                    colModel: [{
                            label: 'No',
                            name: 'no',
                            width: 30,
                            align: "center",
                            sortable: false
                        },
                        {
                            label: 'Bulan',
                            name: 'bulan',
                            width: 40,
                            align: "center",
                            sortable: true
                        },
                        {
                            label: 'Jumlah Invoice',
                            name: 'total_invoice',
                            width: 40,
                            align: "center",
                            sortable: true
                        },
                        {
                            label: 'Nilai Invoice',
                            name: 'nilai_invoice',
                            width: 120,
                            align: "right",
                            formatter: 'currency',
                            formatoptions: {
                                thousandsSeparator: ','
                            },
                            sortable: true
                        },
                        {
                            label: 'Dibayar',
                            name: 'telah_bayar',
                            width: 120,
                            align: "right",
                            formatter: 'currency',
                            formatoptions: {
                                thousandsSeparator: ','
                            },
                            sortable: true
                        },
                        {
                            label: 'Belum Dibayar',
                            name: 'belum_dibayar',
                            width: 120,
                            align: "right",
                            formatter: 'currency',
                            formatoptions: {
                                thousandsSeparator: ','
                            },
                            sortable: true
                        },
                    ],
                    pager: "#jqGridPagerTotal",
                    rowNum: 20,
                    rowList: [10, 20, 50],
                    viewrecords: true,
                    autowidth: true,
                    loadonce: true,
                    serverPaging: true,
                    height: 'auto',
                    jsonReader: {
                        repeatitems: false,
                        root: "rows",
                        page: "page",
                        total: "total",
                        records: "records"
                    },
                    loadComplete: function(data) {
                        // Mengambil nilai sum_telah_bayar dari luar rows
                        var sumTelahBayar = data.sum_telah_bayar;
                        var sumBelumBayar = data.sum_belum_bayar;
                        var countInvoice = data.count_invoice;
                        var sumInvoice = data.sum_nilai_invoice;

                        // Menambahkan sum_telah_bayar ke footer
                        $("#table-lp-total").jqGrid('footerData', 'set', {
                            "bulan": "Total",
                            "total_invoice": countInvoice,
                            "telah_bayar": sumTelahBayar,
                            "belum_dibayar": sumBelumBayar,
                            "nilai_invoice": sumInvoice // Menampilkan sum_telah_bayar di footer
                        });
                    },
                    footerrow: true,
                    userDataOnFooter: true,
                });

                $("#table-lp-total").jqGrid('navGrid', '#jqGridPagerTotal', {
                    edit: false,
                    add: false,
                    del: false,
                    search: true,
                    refresh: true
                });

                $("#table-lp-total").jqGrid('filterToolbar');
            });
        </script>
    </x-slot:script>
</x-Layout.layout>
