<x-Layout.layout>
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

    <!-- Script untuk memuat jqGrid -->
    <x-slot:script>
        <script type="text/javascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>

        <script>
            $(document).ready(function () {
                $("#table-lp").jqGrid({
                    url: "{{ route('laporan.DataPiutang') }}", // URL untuk mengambil data dari controller
                    datatype: "json",
                    mtype: "GET",
                    colModel: [
                        { label: 'No', name: 'no', width: 30, align: "center", sortable: false }, // Kolom nomor urut
                        { label: 'Invoice', name: 'invoice', width: 100, align: "center" },
                        { label: 'Nama Customer', name: 'customer', width: 120, align: "left" }, // Kolom customer
                        { label: 'Jumlah Harga (INC.PPN)', name: 'jumlah_harga', width: 120, align: "right", formatter: 'currency', formatoptions: { thousandsSeparator: ',' } },
                        { label: 'Ditagih TGL', name: 'ditagih_tgl', width: 50, align: "center", formatter: 'date', formatoptions: { newformat: 'Y-m-d' } },
                        { label: 'Jatuh Tempo TGL', name: 'tempo', width: 50, align: "center", formatter: 'date', formatoptions: { newformat: 'Y-m-d' } },
                        { label: 'Dibayar TGL', name: 'dibayar_tgl', width: 50, align: "center", formatter: 'date', formatoptions: { newformat: 'Y-m-d' } },
                        { label: 'Sebesar', name: 'sebesar', width: 120, align: "right", formatter: 'currency', formatoptions: { thousandsSeparator: ',' } },
                        { label: 'Kurang Bayar', name: 'kurang_bayar', width: 120, align: "right", formatter: 'currency', formatoptions: { thousandsSeparator: ',' } },
                    ],
                    pager: "#jqGridPager", // ID untuk pager
                    rowNum: 20, // Menampilkan 20 baris per halaman
                    rowList: [10, 20, 50], // Pilihan jumlah baris per halaman
                    viewrecords: true, // Menampilkan total jumlah record
                    autowidth: true,
                    height: 'auto',
                    jsonReader: {
                        root: "rows", // Mengambil data dari 'rows' pada response JSON
                        page: "page", // Halaman saat ini
                        total: "total", // Total halaman
                        records: "records", // Total jumlah data
                        id: "id" // ID untuk setiap baris
                    },
                    gridComplete: function () {
                        var rowNum = 1;
                        $("#table-lp").find('tr').each(function () {
                            if ($(this).hasClass('jqgrow')) {
                                $(this).find('td:eq(0)').text(rowNum++); // Menambahkan nomor urut
                            }
                        });
                    }
                });

                // Menambahkan fitur filter di toolbar
                $("#table-lp").jqGrid('navGrid', '#jqGridPager', {
                    edit: false,
                    add: false,
                    del: false,
                    search: false,
                    refresh: true
                });

                // Mengaktifkan filter toolbar
                $("#table-lp").jqGrid('filterToolbar');
            });
        </script>
    </x-slot:script>
</x-Layout.layout>
