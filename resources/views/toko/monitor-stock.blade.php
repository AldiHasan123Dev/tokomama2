<x-layout.layout>

    <head>
        <!-- Include jQuery and jqGrid libraries -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/free-jqgrid/4.15.5/css/ui.jqgrid.min.css" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/free-jqgrid/4.15.5/jquery.jqgrid.min.js"></script>
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" />
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>

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
            width: 100%;
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

        .table-custom {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }

                .table-custom thead th {
                    background-color: #f1f1f1;
                    border: 1px solid #ddd;
                    padding: 10px;
                    text-align: left;
                }

                .table-custom tbody td,
                .table-custom tbody th {
                    border: 1px solid #ddd;
                    padding: 8px;
                }

                .table-custom tbody tr:nth-child(even) {
                    background-color: #f9f9f9;
                }

                .table-custom tbody tr:hover {
                    background-color: #f1f1f1;
                }

                .table-custom th,
                .table-custom td {
                    text-align: right;
                }

                #table-buku-besar {
                    width: 100%;
                    /* Pastikan tabel mengambil lebar penuh dari kontainer */
                    border-collapse: collapse;
                    /* Menghilangkan jarak antara border sel */
                }

                #table-buku-besar th,
                #table-buku-besar td {
                    padding: 5px;
                    /* Menambah padding untuk sel */
                    border: 1px solid #ddd;
                    /* Menambah border pada sel */
                }

                #table-buku-besar th {
                    background-color: #f2f2f2;
                    /* Warna latar belakang header */
                }
    </style>

    {{-- <x-keuangan.card-keuangan>
        <x-slot:tittle>List Stock</x-slot:tittle>
        <div class="overflow-x-auto">
            <a class="mt-3 mb-2 mr-3 btn bg-green-500 text-white font-bold hover:bg-gray-700 mb-2px" href="{{ route('barang_masuk') }}">Input Barang Masuk</a>
            <!-- Adjusted table ID and pager ID for jqGrid -->
            <table id="jqGrid" class="table"></table>
            <div id="jqGridPager"></div>
        </div>
    </x-keuangan.card-keuangan> --}}

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Monitoring Stock</x-slot:tittle>
        <div class="overflow-x-auto">
            {{-- <a href="{{ route('stock.csv') }}">
                <button type="button" class="btn font-semibold bg-green-500 btn-sm text-white mt-4">
                    Unduh CSV 
                </button>
            </a>     
            <p style="color: black; padding: 5px; display: inline-block;">
                <span style="color: rgb(74, 144, 71); font-weight: bold;">(Compatible Excel 2021, tetapi perlu  olahan lanjut. Ini bersifat</span>
                <span style="color: red; font-weight: bold;">darurat</span> 
                <span  style="color: rgb(74, 144, 71); font-weight: bold;">sebelum release menu Kartu Stock</span>)
            </p>             --}}
                <div class="overflow-x-auto mt-5">
                    <div class="table-responsive">
                        <!-- Checkbox "Select All" di luar tabel -->
                        <table class="table" id="table-getfaktur"></table>
                        <div id="jqGridPager"></div>
                    </div>
                </div>
                <!-- Adjusted table ID and pager ID for jqGrid -->
                <table id="jqGrid1" class="table"></table>
                <div id="jqGridPager1"></div>
                <div class="container mt-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-success" style="background: #e2190a !important;">
                                <div class="card-body">
                                    <h5 class="card-title text-success" style="color: #ffffff;">Persediaan Dalam Perjalanan </h5>
                                    <p class="card-text fs-4 fw-bold text-success" style="color: #ffffff;">Rp {{ number_format($perjalanan, 0, ',', '.') }} (Terjurnal HTG Rp {{ number_format($perjalanan2, 0, ',', '.') }}) , (Belum Terjurnal Rp {{ number_format($perjalanan1, 0, ',', '.') }})</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm border-teal" style="background: #20c997 !important;">
                                <div class="card-body">
                                    <h5 class="card-title" style="color: #ffffff;">Persediaan Jayapura</h5>
                                    <p class="card-text fs-4 fw-bold" style="color: #ffffff;">Rp {{ number_format($jayapura, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>         
            </div>
        </x-keuangan.card-keuangan>
        <x-keuangan.card-keuangan>
            <x-slot:tittle>Kartu Stock</x-slot:tittle>
        <div class="card bg-white shadow-sm">
            <div class="card-body">
                <div class="grid grid-cols-4">
                    <div class="font-bold">Nama Barang : </div>
                    <form method="GET" action="{{ route('monitor-stock') }}" id="formBarang">
                        <div>
                            <select class="js-example-basic-single w-1/2" name="barang" id="barang">
                                <option value="" selected>Pilih Barang</option>
                                @foreach ($barang as $b)
                                <option value="{{ $b->id }}" {{ request('barang') == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama ?? '-' }} - {{ $b->satuan->nama_satuan ?? '-' }}
                                </option>                                
                                @endforeach
                            </select>
                        </div>
                    </form>                                 
                    <div class="font-bold">Tahun : </div>
                    <div>
                        <select class="js-example-basic-single w-1/2" name="akun" id="thn">
                            <option selected value="{{ date('Y') }}">{{ date('Y') }}</option>
                            @for ($year = date('Y'); $year >= 2025; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                @php
                $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            
                // Inisialisasi array kosong per bulan
                $masuk = $keluar = $sisa = $harga_jual = $harga_beli = $stock_sisa = array_fill(0, 12, 0);
            
                // Loop hasil dan akumulasi per bulan
                foreach ($hasil as $barang) {
                    foreach ($barang['detail'] as $row) {
                        $index = $row['index_bulan'] ?? null;
            
                        if (is_numeric($index) && $index >= 0 && $index < 12) {
                            $masuk[$index]      += $row['jumlah_beli'] ?? 0;
                            $stock_sisa[$index]      += $row['stock_awal'] ?? 0;
                            $keluar[$index]     += $row['jumlah_jual'] ?? 0;
                            $sisa[$index]       += $row['sisa_stock'] ?? 0;
                            $harga_jual[$index] += $row['harga_jual'] ?? 0;
                            $harga_beli[$index] += $row['harga_beli'] ?? 0;
                        }
                    }
                }
            @endphp
            
            <table class="table-custom table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        @foreach ($months as $month)
                            <th>{{ $month }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Stock Awal</th>
                        @foreach ($stock_sisa as $value)
                            <td>{{ number_format($value) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Barang Masuk</th>
                        @foreach ($masuk as $value)
                            <td>{{ number_format($value) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Barang Keluar</th>
                        @foreach ($keluar as $value)
                            <td>{{ number_format($value) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Stock Sisa</th>
                        @foreach ($sisa as $value)
                            <td>{{ number_format($value) }}</td>
                        @endforeach
                    </tr>
                    {{-- Uncomment jika mau tampil harga beli & jual
                    <tr>
                        <th>Harga Beli</th>
                        @foreach ($harga_beli as $value)
                            <td>{{ number_format($value) }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Harga Jual</th>
                        @foreach ($harga_jual as $value)
                            <td>{{ number_format($value) }}</td>
                        @endforeach
                    </tr>
                    --}}
                </tbody>
            </table>
            
            </div>
        </div>
        @php
        $barang = request()->get('barang');
        $selectedMonth = request()->get('month');
        $year = request()->get('year', date('Y'));
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];
    @endphp
    
    <form method="GET" action="{{ route('monitor-stock') }}" id="filterForm" class="flex gap-2 mt-4 mb-6">
        @foreach ($months as $num => $name)
            <button type="submit" name="month" value="{{ $num }}"
                class="px-4 py-3 border-2 border-green-600 hover:bg-green-600 hover:text-white duration-300 rounded-xl
                {{ $selectedMonth == $num ? 'bg-green-500 text-white' : '' }}">
                {{ $name }}
            </button>
        @endforeach
        <input type="hidden" name="year" value="{{ $year }}">
        <input type="hidden" name="barang" id="hiddenBarang" value="{{ $barang }}">
    </form>

    <div class="row">
        @foreach ($hasil1 as $barang)
            <div class="col-md-2 mb-1">
                <div class="card shadow-sm border-teal" style="background: #11926b !important; border-radius: 5px;">
                    <div class="card-body">
                        <h5 class="card-title" style="color: #ffffff; font-size: 14px; font-weight: bold;">
                            Rincian Stock Barang : <span style="color: #FFD700; font-weight: bold;">{{ $barang['barang'] }}</span> <!-- Menambahkan warna mencolok -->
                        </h5>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    

    <div class="table-responsive">
        <table id="table-buku-besar" class="cell-border hover display nowrap compact">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tanggal</th>
                    <th>No. BM</th>
                    <th>No. SJ</th>
                    <th>Tgl SJ</th>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Jumlah Beli</th>
                    <th>Jumlah Jual</th>
                    <th>Sisa</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach ($hasil1 as $barang)
                    @foreach ($barang['detail'] as $detail)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $detail['tgl'] }}</td>
                            <td>{{ $detail['no'] }}</td>
                            <td>{{ $detail['surat_jalan'] }}</td>
                            <td>{{ $detail['tgl_sj'] }}</td>
                            <td>{{ $detail['invoice'] }}</td>
                            <td>{{ $detail['customer'] }}</td>
                            <td>{{ $detail['tipe'] === 'beli' ? number_format($detail['jumlah']) : '-' }}</td>
                            <td>{{ $detail['tipe'] === 'jual' ?number_format($detail['jumlah']) : '-' }}</td>
                            <td>{{ number_format($detail['sisa_stock']) }}</td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>                   
        </table>
    </div>    
    

    </x-keuangan.card-keuangan>

    <x-slot:script>
        <script src="https://cdn.datatables.net/2.1.0/js/dataTables.tailwindcss.js"></script>
        <script>
             
    $(document).ready(function() {
        $('.js-example-basic-single').select2();

        $('#barang').on('change', function () {
            $('#formBarang').submit();
        });
        var table = $('#table-buku-besar').DataTable({
                pageLength: 25,
                scrollX: true, 
            });
    });


$(function () {
    const table1 = $("#jqGrid1").jqGrid({
        url: "{{ route('stock.data2') }}", // URL untuk data JSON dari controller
        datatype: "json",
        mtype: "GET",
        colModel: [
            {
                label: 'No',
                name: 'index',
                align: 'center',
                width: 20
            },
            {
                            name: 'id',
                            index: 'id',
                            hidden: true
            },
            {
                label: 'Status',
                name: 'status',
                width: 200,
                sortable: false,
            },
            {
                search: true,
                label: 'Invoice Supp',
                name: 'invoice_external',
                width: 100
            },
            {
                label: 'Nomor Jurnal',
                name: 'jurnal',
                align: "center",
                width: 100,
                sortable: false,
            },
            {
    label: 'Tgl Konfirmasi',
    name: 'tgl_jurnal',
    align: "center",
    width: 100,
    sortable: false,
},
            {
                search: true,
                label: 'No BM',
                name: 'no_bm',
                width: 100
            },
            {
                search: true,
                label: 'Barang',
                name: 'barang.nama',
                width: 100
            },
            {
                search: true,
                label: 'Volume Masuk',
                name: 'total_beli',
                width: 80,
                align: "right"
            },
            {
                search: true,
                label: 'Volume Keluar',
                name: 'total_jual',
                width: 80,
                align: "right"
            },
            {
                search: true,
                label: 'Satuan Beli',
                name: 'satuan_beli',
                width: 70,
                align: "center"
            },
            {
                search: true,
                label: 'Satuan Jual',
                name: 'satuan_jual',
                width: 70,
                align: "center"
            },
            {
                search: true,
                label: 'Sisa',
                name: 'sisa',
                width: 50,
                align: "right"
            },
            {
                search: true,
                label: 'Harga Beli',
                name: 'total_harga_beli',
                width: 80,
                align: "right",
                formatter: "number",
                formatoptions: {
                    decimalSeparator: ".",
                    thousandsSeparator: ",",
                    decimalPlaces: 2, // Jumlah angka di belakang koma
                    defaultValue: "0.00" // Nilai default jika kosong
                }
            }
        ],
        pager: "#jqGridPager1", // Link pager dengan ID yang benar
        rowNum: 20,
        rowList: [50, 100, 500], // Pilihan jumlah baris
        height: 'auto', // Tinggi tabel otomatis menyesuaikan
        viewrecords: true, // Menampilkan jumlah total data
        autowidth: true, // Menyesuaikan lebar tabel dengan kontainer
        caption: "Data Stock", // Judul tabel
        loadonce: true, // Memuat semua data hanya sekali
        search: true, // Mengaktifkan toolbar pencarian
        toolbar: [true, "top"], // Menampilkan toolbar di bagian atas tabel
        filterToolbar: true, // Mengaktifkan filter toolbar
        gridComplete: function () {
            // Optional: Tambahkan logika jika diperlukan
        }
    });

    // Aktifkan filter toolbar (opsional jika sudah diaktifkan di opsi)
    $("#jqGrid1").jqGrid('filterToolbar');
});

        </script>
    </x-slot:script>

</x-layout.layout>
