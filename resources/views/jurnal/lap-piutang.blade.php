<x-Layout.layout>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Detail Laporan Piutang</x-slot:tittle>
        <table class="table" id="table-lp"></table> <!-- ID yang digunakan -->
        <div id="jqGridPager"></div>
    </x-keuangan.card-keuangan>
    <x-slot:script>
        <script type="text/ecmascript" src="{{ asset('assets/js/grid.locale-en.js') }}"></script>
        <script type="text/ecmascript" src="{{ asset('assets/js/jquery.jqGrid.min.js') }}"></script>

        <script>
            $(document).ready(function () {
                $("#table-lp").jqGrid({ // Ganti ID yang digunakan
                    url: "{{ route('laporan.DataPiutang') }}", // URL untuk mengambil data JSON dari controller
                    datatype: "json",
                    mtype: "GET",
                    colModel: [
                        { label: 'No', name: 'no', width: 50, align: "center", sortable: false }, // Kolom nomor urut
                        { label: 'Invoice', name: 'invoice', width: 150, align: "center" },
                        { label: 'Nama Customer', name: 'nama_customer', width: 200, align: "left" },
                        { label: 'Jumlah Harga (INC.PPN)', name: 'subtotal', width: 120, align: "right", formatter: 'currency', formatoptions: { prefix: 'Rp', thousandsSeparator: ',' } },
                        { label: 'Ditagih TGL', name: 'tgl_invoice', width: 150, align: "center", formatter: 'date', formatoptions: { newformat: 'Y-m-d' } },
                        { label: 'Dibayar TGL', name: 'tgl', width: 150, align: "center", formatter: 'date', formatoptions: { newformat: 'Y-m-d' } },
                        { label: 'Sebesar', name: 'debit', width: 120, align: "right", formatter: 'currency', formatoptions: { prefix: 'Rp', thousandsSeparator: ',' } },
                    ],
                    pager: "#jqGridPager", // Pastikan pager memiliki ID yang benar
                    rowNum: 20, // Menampilkan 20 baris per halaman
                    rowList: [10, 20, 50], // Pilihan jumlah baris per halaman
                    viewrecords: true, // Menampilkan total jumlah record
                    autowidth: true,
                    height: 'auto',
                    jsonReader: {
                        root: "data", // Sesuaikan root data untuk jqGrid
                        page: "current_page", // Menyelaraskan dengan struktur JSON yang diterima
                        total: "total_pages", // Total halaman
                        records: "total_records", // Total jumlah data
                        id: "id" // ID untuk setiap baris
                    },
                    gridComplete: function () {
                        var rowNum = 1;
                        $("#table-lp").find('tr').each(function () { // Ganti ID yang digunakan
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
