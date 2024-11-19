<x-Layout.layout>

    <style>
        /* CSS untuk memberikan jarak antar invoice */
        .nilai_invoice {
            line-height: 1.5;
            /* Jarak antar baris */
            margin-bottom: 5px;
            /* Jarak bawah antar invoice */
        }
    </style>
    <!-- Link CSS untuk jqGrid dan jQuery UI -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />

    <!-- Card untuk tampilan laporan piutang -->
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Detail Laporan Piutang</x-slot:tittle>

        <!-- Tabel untuk menampilkan data menggunakan jqGrid -->
        <table class="table" id="table-lp"></table>

        <!-- Pager untuk navigasi halaman -->
        <div id="jqGridPager"></div>
    </x-keuangan.card-keuangan>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Summary Laporan Piutang</x-slot:tittle>

        <!-- Tabel untuk menampilkan data menggunakan jqGrid -->
        <table class="table" id="table-lp-total"></table>

        <!-- Pager untuk navigasi halaman -->
        <div id="jqGridPagerTotal"></div>
    </x-keuangan.card-keuangan>

    <!-- Script untuk memuat jqGrid -->
    <x-slot:script>
        <script type="text/javascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>

        <script>
            $(document).ready(function() {
                $("#table-lp").jqGrid({
                    url: "{{ route('laporan.DataPiutang') }}", // URL untuk mengambil data dari controller
                    datatype: "json",
                    mtype: "GET",
                    colModel: [{
                            search: true,
                            label: 'No',
                            name: 'no',
                            width: 30,
                            align: "center",
                            sortable: false
                        }, // Kolom nomor urut
                        {
                            search: true,
                            label: 'Invoice',
                            name: 'invoice',
                            width: 100,
                            align: "center",
                            sortable: true
                        },
                        {
                            search: true,
                            label: 'Nama Customer',
                            name: 'customer',
                            width: 120,
                            align: "left",
                            sortable: true
                        }, // Kolom customer
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
                            search: true,
                            label: 'Ditagih TGL',
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
                            formatter: 'date',
                            formatoptions: {
                                newformat: 'Y-m-d'
                            },
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
                            sortable: true,
                            cellattr: function(rowId, val) {
                                // Pastikan nilai adalah angka dan lebih besar dari 0
                                if (parseFloat(val.replace(/[^0-9.-]+/g, "")) > 0) {
                                    return 'style="background-color:red;color:white;"';
                                }
                            }
                        }

                    ],
                    pager: "#jqGridPager", // ID untuk pager
                    rowNum: 20, // Menampilkan 20 baris per halaman
                    rowList: [10, 20, 50], // Pilihan jumlah baris per halaman
                    viewrecords: true, // Menampilkan total jumlah record
                    autowidth: true,
                    loadonce: true, // Memungkinkan pagination server-side
                    height: 'auto',
                    jsonReader: {
                        repeatitems: false,
                        root: "rows", // Mengambil data dari 'rows' pada response JSON
                        page: "page", // Halaman saat ini
                        total: "total", // Total halaman
                        records: "records", // Total jumlah data
                    },
                    loadComplete: function(data) {
                        console.log("Load complete: ",
                        data); // Menampilkan data yang diterima saat grid selesai dimuat
                        $("#table-lp").jqGrid(
                        'filterToolbar'); // Mengaktifkan filter toolbar setelah data dimuat
                    }
                });

                // Menambahkan fitur filter di toolbar
                $("#table-lp").jqGrid('navGrid', '#jqGridPager', {
                    edit: false,
                    add: false,
                    del: false,
                    search: true, // Mengaktifkan tombol pencarian
                    refresh: true
                });

                // Mengaktifkan filter toolbar
                $("#table-lp").jqGrid('filterToolbar');
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
