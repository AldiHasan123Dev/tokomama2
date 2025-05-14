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
            max-height: 500px; /* Sesuaikan tinggi maksimum agar tidak terlalu panjang */
            margin-top: 20px;
            border: 1px solid #ddd;
        }

        .table-cus {
            border-collapse: collapse;
            width: 100%;
            min-width: 800px; /* Pastikan tabel tidak terlalu sempit */
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            font-size: 12px;
        }

        .table-cus th, .table-cus td {
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
            background-color: rgb(0, 0, 0); /* Sesuaikan warna agar tidak menutupi konten */
            font-weight: bold;
            z-index: 2;
        }


        .table-cus .bg-total1 { background-color: #473f39; color: white; font-weight: bold; text-align: right; }
        .table-cus .bg-total-thn { background-color: #0f2d44; color: white; font-weight: bold; }
        .table-cus .bg-total-monthly { background-color: #098e0c;  color: white; font-weight: bold; text-align: right;}
        .table-cus .bg-total-monthly1 { background-color: #546816;  color: white; font-weight: bold; text-align: right;}
        .table-cus .bg-total { background-color: #93685b; color: white; font-weight: bold;}
        .table-cus .kurang-bayar { background-color: #f8f81f; color: rgb(0, 0, 0); font-weight: bold;}
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
<div class="flex flex-row gap-4 mb-16 mt-8">
    <label class="form-control w-full max-w-xs mb-1">
        <div class="label">
            <span class="label-text">Cari Bulan Inv</span>
        </div>
        <input type="month"
            class="input input-sm input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
            id="tgl_inv" name="tgl_inv" autocomplete="off" value="{{ date('Y-m') }}" />
    </label>

    <label class="form-control w-full max-w-xs mb-1">
        <div class="label">
            <span class="label-text">Cari Invoice di bulan tersebut</span>
        </div>
        <input type="text"
            class="input input-sm input-bordered w-full max-w-xs rounded-lg bg-transparent dark:text-white"
            id="inv" name="inv" autocomplete="off" />
    </label>
</div>


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

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Monitoring Piutang Customer</x-slot:tittle>
    
        <!-- Dropdown untuk memilih tahun -->
        <form action="{{ route('laporan.Piutang') }}" method="GET">
            <div class="form-container">
                <div class="mb-3 mt-3">
                    <label for="year" class="form-label">Pilih Tahun</label>
                    <div class="input-group">
                        <select name="year" id="year" class="form-select">
                            <option value="" disabled selected>Pilih Tahun</option>
                            @foreach ($years as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </div>
        </form>
    
        <div class="table-container">
            <table class="table-cus">
                <thead>
                    <tr>
                        <th class="bg-thn" rowspan="3">Customer</th>
                        <th class="bg-thn" colspan="{{ count($months) * 2 }}">Bulan</th>
                        <th class="bg-total" rowspan="3">Total</th>
                    </tr>
                    <tr>
                        @foreach ($months as $month)
                            <th class="text-center" colspan="2">{{ $month }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($months as $month)
                            <th>Inv</th>
                            <th>Total</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mergedResults as $customer_id => $customerData)
                        @php $totalOmzet = 0; @endphp
                        <tr>
                            <td class="bg-thn">{{ $customerData['customer_name'] }}</td>
                        
                            @foreach ($months as $month)
                                @php
                                    $data = $customerData['years'][request('year') ?? 2025][$month] ?? null;
                        
                                    $inv = isset($data['selisih_invoice']) ? (int) $data['selisih_invoice'] : 0;
                                    $omzet = isset($data['omzet']) ? (int) $data['omzet'] : 0;
                        
                                    $totalOmzet += $omzet;
                        
                                    // Tandai warna hanya jika selisih_invoice == 0 dan omzet > 0
                                    $warningClass = ($inv === 0 && $omzet > 0) ? 'kurang-bayar' : '';
                                @endphp
                        
                                <td class="text-center {{ $warningClass }}">
                                    {{ $inv === 0 ? '-' : $inv }}
                                </td>
                                <td class="text-end {{ $warningClass }}">
                                    {{ number_format($omzet, 0, ',', '.') }}
                                </td>
                            @endforeach
                        
                            <td class="bg-total text-end">{{ number_format($totalOmzet, 0, ',', '.') }}</td>
                        </tr>                                               
                    @endforeach
    
                    {{-- Footer Total Per Bulan --}}
                    <tr class="sticky-footer">
                        <td class="bg-total-thn">{{ request('year') ?? 2025 }}</td>
                        @php
                            $totalYearlyOmzet = 0;
                            $totalInvoiceCount = 0;
                        @endphp
                        @foreach ($months as $month)
                            @php
                                $monthlyOmzet = $monthlyTotals[request('year') ?? 2025][$month] ?? 0;
                                $invoiceCount = $monthlySelisihInvoice[request('year') ?? 2025][$month] ?? 0;
                                $totalYearlyOmzet += $monthlyOmzet;
                                $totalInvoiceCount += $invoiceCount;
                            @endphp
                           <td class="bg-total-monthly1 text-end">
                            {{ $invoiceCount == 0 ? '-' : $invoiceCount }}
                        </td>                        
                            <td class="bg-total-monthly text-end">{{ number_format($monthlyOmzet, 0, ',', '.') }}</td>
                        @endforeach
                        <td class="bg-total1 text-end">{{ number_format($totalYearlyOmzet, 0, ',', '.') }}</td>
                    </tr>                    
                </tbody>
            </table>
        </div>
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
    $("#table-lp").jqGrid({
        url: "{{ route('laporan.DataPiutang') }}", 
        datatype: "json",
        postData: {
                    tgl_inv: function() {
                        return $('#tgl_inv').val();
                    },
                    
            inv: function () {
                return $('#inv').val();
            }
                },
        mtype: "GET",
        colModel: [
            { search: true, label: 'Invoice', name: 'invoice', width: 100, align: "center", sortable: true },
            { search: true, label: 'Nama Customer', name: 'customer', width: 120, align: "left", sortable: true },
            { search: true, label: 'Harga (INC.PPN)', name: 'jumlah_harga', width: 120, align: "right", formatter: 'currency', formatoptions: { thousandsSeparator: ',' }, sortable: true },
            { name: 'tanggal', label: 'Tanggal', width: 50, align: "center", formatter: 'date', formatoptions: { newformat: 'Y-m-d' }, sortable: true, hidden: true },
            { search: true, label: 'TGL Invoice', name: 'ditagih_tgl', width: 50, align: "center", formatter: 'date', formatoptions: { newformat: 'Y-m-d' }, sortable: true },
            { search: true, label: 'TOP', name: 'top', width: 30, align: "center", sortable: true },
            { search: true, label: 'Jatuh Tempo TGL', name: 'tempo', width: 50, align: "center", formatter: 'date', formatoptions: { newformat: 'Y-m-d' }, sortable: true },
            { search: true, label: 'Dibayar TGL', name: 'dibayar_tgl', width: 50, align: "center", sortable: true },
            { search: true, label: 'Dibayar', name: 'sebesar', width: 120, align: "right", formatter: 'currency', formatoptions: { thousandsSeparator: ',' }, sortable: true },
            { search: true, label: 'Kurang Bayar', name: 'kurang_bayar', width: 120, align: "right", formatter: 'currency', formatoptions: { thousandsSeparator: ',' }, sortable: true }
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
        return { "style": "background-color: #3fae43; color: white;" };
    }

    // Jika TOP = 0 dan jatuh tempo hari ini, tidak diberi warna
    if (parseInt(rowData.top) === 0 && tempoDate === today) {
        return {};
    }

    // Warna oranye untuk jatuh tempo dalam 1-3 hari
    if (daysDiff > 0 && daysDiff <= 4) {
        return { "style": "background-color: orange;" };
    } 
    
    // Warna merah jika sudah jatuh tempo atau jatuh tempo hari ini
    if (daysDiff < 0) {
        return { "style": "background-color: red; color: white;" };
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
