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
        <x-slot:tittle>Report Outstanding Piutang Cust</x-slot:tittle>
        <p class="server-time">
            Tanggal dan Waktu Server : <span id="server-time">{{ now()->format('Y-m-d H:i:s') }}</span>
        </p>
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
            document.addEventListener("DOMContentLoaded", function () {
             // Fungsi untuk memperbarui waktu
             function updateServerTime() {
                 fetch("{{ route('server.time') }}")
                     .then(response => response.json()) // Mengambil data JSON dari server
                     .then(data => {
                         document.getElementById("server-time").textContent = data.time; // Update elemen dengan id "server-time"
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
    $("#table-lp").jqGrid({
        url: "{{ route('laporan.DataPiutang') }}", 
        datatype: "json",
        mtype: "GET",
        colModel: [
            { search: true, label: 'No', name: 'no', width: 30, align: "center", sortable: false },
            { search: true, label: 'Invoice', name: 'invoice', width: 100, align: "center", sortable: true },
            { search: true, label: 'Nama Customer', name: 'customer', width: 120, align: "left", sortable: true },
            { search: true, label: 'Harga (INC.PPN)', name: 'jumlah_harga', width: 120, align: "right", formatter: 'currency', formatoptions: { thousandsSeparator: ',' }, sortable: true },
            { name: 'tanggal', label: 'Tanggal', width: 50, align: "center", formatter: 'date', formatoptions: { newformat: 'Y-m-d' }, sortable: true, hidden: true },
            { search: true, label: 'TGL Invoice', name: 'ditagih_tgl', width: 50, align: "center", formatter: 'date', formatoptions: { newformat: 'Y-m-d' }, sortable: true },
            { search: true, label: 'TOP', name: 'top', width: 30, align: "center", sortable: true },
            { search: true, label: 'Jatuh Tempo TGL', name: 'tempo', width: 50, align: "center", formatter: 'date', formatoptions: { newformat: 'Y-m-d' }, sortable: true },
            { search: true, label: 'Dibayar TGL', name: 'dibayar_tgl', width: 50, align: "center", formatter: 'date', formatoptions: { newformat: 'Y-m-d' }, sortable: true },
            { search: true, label: 'Dibayar', name: 'sebesar', width: 120, align: "right", formatter: 'currency', formatoptions: { thousandsSeparator: ',' }, sortable: true },
            { search: true, label: 'Kurang Bayar', name: 'kurang_bayar', width: 120, align: "right", formatter: 'currency', formatoptions: { thousandsSeparator: ',' }, sortable: true }
        ],
        pager: "#jqGridPager",
        rowNum: 100,
        rowList: [150, 200],
        viewrecords: true,
        autowidth: true,
        loadonce: true,
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
    
    let today = new Date();
    let tempoDate = new Date(rowData.tempo);
    let timeDiff = tempoDate - today;
    let daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));

    console.log(`Row Data: ${JSON.stringify(rowData)}`);
    console.log(`Tanggal Hari Ini: ${today.toISOString().split('T')[0]}`);
    console.log(`Jatuh Tempo: ${rowData.tempo}`);
    console.log(`Selisih Hari: ${daysDiff}`);
    console.log(`TOP: ${rowData.top}`);
    console.log(`Kurang Bayar: ${rowData.kurang_bayar}`);

    // Jika kurang bayar = 0, semua kondisi tetap hijau
    if (parseFloat(rowData.kurang_bayar) === 0) {
        console.log(`Lunas: Kurang bayar 0!`);
        return { "style": "background-color: #3fae43; color: white;" };
    }

    // Jika TOP = 0, tidak diberi warna
    if (parseInt(rowData.top) === 0) {
        console.log(`TOP 0: Tidak diberi warna`);
        return {};
    }

    // Warna oranye untuk jatuh tempo dalam 1-3 hari
    if (daysDiff > 0 && daysDiff <= 3) {
        console.log(`Warning: Jatuh tempo dalam ${daysDiff} hari!`);
        return { "style": "background-color: orange;" };
    } 
    
    // Warna merah jika sudah jatuh tempo atau jatuh tempo hari ini
    if (daysDiff < 0 || daysDiff == 0) {
        console.log(`Overdue: Sudah lewat jatuh tempo ${Math.abs(daysDiff)} hari!`);
        return { "style": "background-color: red; color: white;" };
    }

    return {};
},
        loadComplete: function(data) {
            console.log("Load complete: ", data);
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
