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
    </style>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>List Barang Masuk</x-slot:tittle>
        <div class="overflow-x-auto">
            <!-- Adjusted table ID and pager ID for jqGrid -->
            <table id="jqGrid" class="table"></table>
            <div id="jqGridPager"></div>
        </div>
    </x-keuangan.card-keuangan>
    <x-slot:script>
        <script>
            $(function() {
                $("#jqGrid").jqGrid({
                    url: "{{ route('stock.data') }}", // URL untuk data JSON dari controller
                    datatype: "json",
                    mtype: "GET",
                    colModel: [
                        {
                            search: true,
                            label: 'No BM',
                            name: 'no_bm',
                            width: 50,
                            align: "center"
                        },
                        {
                            search: true,
                            label: 'Tgl Barang Masuk',
                            name: 'tgl_masuk',
                            width: 80,
                            align: "center" // Mengatur alignment menjadi center
                        },
                        {
                        label: 'Aksi',
                        name: 'aksi',
                        width: 90,
                        align: 'center',
                        sortable: false,
                        formatter: 'unformat'
                    },
                        {
                            search: true,
                            label: 'Volume Masuk',
                            name: 'vol_bm',
                            width: 100,
                            align: "center"
                        }
                    ],
                    pager: "#jqGridPager", // Link pager dengan ID yang benar
                    rowNum: 10,
                    rowList: [10, 100, 500], // Pilihan jumlah baris
                    height: 'auto', // Tinggi tabel otomatis menyesuaikan
                    viewrecords: true, // Menampilkan jumlah total data
                    autowidth: true, // Menyesuaikan lebar tabel dengan kontainer
                    caption: "List Barang Masuk", // Judul tabel
                    loadonce: true, // Memuat semua data hanya sekali
                    search: true, // Mengaktifkan toolbar pencarian
                    toolbar: [true, "top"], // Menampilkan toolbar di bagian atas tabel
                    filterToolbar: true, // Mengaktifkan filter toolbar
                    gridComplete: function () {
                        // Optional: You can customize what happens after the grid loads
                    }
                });
                $("#jqGrid").jqGrid('filterToolbar');
            });
        </script>
    </x-slot:script>
</x-layout.layout>