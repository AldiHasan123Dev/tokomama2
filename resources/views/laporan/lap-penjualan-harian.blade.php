<x-Layout.layout>

    <style>
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
            .ui-jqgrid {
    border: 2px solid #ccc !important; /* Ubah warna border sesuai keinginan */
}

.ui-jqgrid .ui-jqgrid-btable, 
.ui-jqgrid .ui-jqgrid-htable {
    border: 2px solid #aaa !important; /* Border lebih terang */
}

.ui-jqgrid tr.jqgrow td {
    border: 1px solid #857979 !important; /* Border antar sel */
}

.ui-jqgrid .ui-state-default {
    border: 2px solid #bbb !important; /* Border header tabel */
}
    </style>
    <!-- Link CSS untuk jqGrid dan jQuery UI -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />

    <!-- Card untuk tampilan laporan piutang -->
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Daily Sales Report</x-slot:tittle>
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
          $(document).ready(function() {
    $("#table-lp").jqGrid({
        url: "{{ route('laporan.DataPenjualanHarian') }}", 
        datatype: "json",
        mtype: "GET",
        colModel: [
            { search: true, label: 'Tgl Invoice', name: 'tgl_invoice', width: 100, align: "center", sortable: true },
            { 
                search: true, 
                label: 'Invoice', 
                name: 'invoice', 
                width: 120, 
                align: "center", 
                sortable: true,
                formatter: function(cellvalue) {
                    if (!cellvalue || cellvalue === '-') return '-';
                    let invoiceNumbers = cellvalue.split(', '); 
                    return invoiceNumbers.map(num => 
                        `<a href="/keuangan/cetak-invoice/?invoice=${encodeURIComponent(num)}" class="text-primary" target="_blank">${num}</a>` 
                    ).join(', ');
                }
            },
            { search: true, label: 'Nama Customer', name: 'customer', width: 200, align: "left", sortable: true },
            { search: true, label: 'Barang', name: 'barang', width: 650, align: "left", sortable: true },
            { search: true, label: 'Tagihan', name: 'tagihan', align: "right", sortable: true },
            { 
                search: true, 
                label: 'Total', 
                name: 'jumlah_harga', 
                width: 150, 
                align: "right", 
                formatter: 'currency', 
                formatoptions: { thousandsSeparator: ',' }, 
                sortable: true 
            }
        ],
        pager: "#jqGridPager",
        rowNum: 20,
        rowList: [10, 20, 50],
        viewrecords: true,
        autowidth: true,
        shrinkToFit: false,
        height: 'auto',
        loadonce: true,
        jsonReader: {
            repeatitems: false,
            root: "rows",
            page: "page",
            total: "total",
            records: "records"
        },
        gridComplete: function() {
            var grid = $("#table-lp");
            var rowIds = grid.getDataIDs();
            var rowspanMap = {};

            // Hitung jumlah kemunculan tgl_invoice untuk kolom 'Total'
            rowIds.forEach(rowId => {
                var rowData = grid.getRowData(rowId);
                var key = rowData.tgl_invoice;
                if (!rowspanMap[key]) {
                    rowspanMap[key] = { count: 1, firstRowId: rowId };
                } else {
                    rowspanMap[key].count++;
                }
            });

            // Terapkan rowspan hanya pada 'Total'
            Object.keys(rowspanMap).forEach(key => {
                var firstRowId = rowspanMap[key].firstRowId;
                var rowspanCount = rowspanMap[key].count;

                if (rowspanCount > 1) {
                    $("#table-lp tr#" + firstRowId + " td[aria-describedby='table-lp_jumlah_harga']")
                        .attr("rowspan", rowspanCount)
                    
                    rowIds.slice(rowIds.indexOf(firstRowId) + 1, rowIds.indexOf(firstRowId) + rowspanCount).forEach(rowId => {
                        $("#table-lp tr#" + rowId + " td[aria-describedby='table-lp_jumlah_harga']").hide();
                    });
                }
            });
        }
    });

    $("#table-lp").jqGrid('navGrid', '#jqGridPager', {
        edit: false,
        add: false,
        del: false,
        search: true,
        refresh: true
    });

    $("#table-lp").jqGrid('filterToolbar');
    
    // Menambahkan CSS untuk memperjelas semua border di tabel
});
        </script>
    </x-slot:script>
</x-Layout.layout>
