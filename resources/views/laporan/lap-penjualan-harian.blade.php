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
            { search: true, label: 'No', name: 'no', width: 30, align: "center", sortable: false, cellattr: addBorder },
            { search: true, label: 'Tgl Invoice', name: 'tgl_invoice', width: 100, align: "center", sortable: true, cellattr: addBorder },
    { search: true, label: 'Invoice', name: 'invoice', width: 120, align: "center", sortable: true, cellattr: addBorder,
            formatter: function(cellvalue, options, rowObject) {
        if (!cellvalue || cellvalue === '-') {
            return '-'; // Jika tidak ada nomor jurnal, tampilkan tanda "-"
        }
        let invoiceNumbers = cellvalue.split(', '); // Memisahkan nomor jurnal jika lebih dari satu
        let links = invoiceNumbers.map(num => {
            let encodedNum = encodeURIComponent(num); // Encode nomor jurnal agar sesuai dengan URL
            return `<a href="/keuangan/cetak-invoice/?invoice=${encodedNum}" class="text-primary" target="_blank" rel="noopener noreferrer">${num}</a>`;y
        });
        return links.join(', '); // Gabungkan semua link dengan koma
    }
     },
    { search: true, label: 'Nama Customer', name: 'customer', width: 200, align: "left", sortable: true, cellattr: addBorder, 
      formatter: formatMultiline },
//       { 
//     search: true, 
//     label: 'Barang', 
//     name: 'barang', 
//     width: 200, 
//     align: "left", 
//     sortable: true, 
//     cellattr: addBorder,
// },
{ search: true, label: 'Tagihan', name: 'tagihan', align: "right", sortable: true, cellattr: addBorder },
            { search: true, label: 'Total', name: 'jumlah_harga', width: 470, align: "right", formatter: 'currency', 
              formatoptions: { thousandsSeparator: ',' }, sortable: true, cellattr: addBorder }
        ],
        pager: "#jqGridPager",
        rowNum: 20,
        rowList: [10, 20, 50],
        viewrecords: true,
        autowidth: true,
        shrinkToFit: false,
        height: 'auto',
        loadonce: true,
        autoResizing: true,
        cellLayout: 5, // Memberikan ruang antar sel
        jsonReader: {
            repeatitems: false,
            root: "rows",
            page: "page",
            total: "total",
            records: "records"
        },
        rowattr: function(rowData) {
            if (!rowData.tempo) return {}; 

            let today = new Date();
            let tempoDate = new Date(rowData.tempo);
            let timeDiff = tempoDate - today;
            let daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

            if (parseFloat(rowData.kurang_bayar) === 0) {
                return { "style": "background-color: #3fae43; color: white;" };
            }

            if (daysDiff > 0 && daysDiff <= 3) {
                return { "style": "background-color: yellow;" };
            } else if (daysDiff < 0 || daysDiff == 0) {
                return { "style": "background-color: red; color: white;" };
            }

            return {};
        },
        loadComplete: function(data) {
            $("#table-lp").jqGrid('filterToolbar');
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
});

// Fungsi untuk memberikan border ke setiap sel
function addBorder(rowId, cellValue, rawObject, colModel, rowData) {
    return 'style="border: 1px solid #ccc; padding: 5px;"';
}

// Fungsi untuk menampilkan data multiline dalam satu sel
function formatMultiline(cellValue, options, rowObject) {
    if (Array.isArray(cellValue)) {
        return cellValue.join("<br>");
    }
    return cellValue;
}



        </script>
    </x-slot:script>
</x-Layout.layout>
